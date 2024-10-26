import AppEvent from "./enums/AppEvent";
import SignalingServer from "./SignalingServer";
import log from "./utils/log";
import Action from "./enums/Action";
import {
    Conversation,
    CountryInfo,
    HighlightInfo,
    Message,
    Notification,
    SignalingMessage, TalkResult,
    VideoSessionBaseInfo,
    VideoSessionInfo
} from "./models";
import EventsEmitter from "./EventsEmitter";
import VideoProcessor from "./VideoProcessor";
import PeerConnection from "./PeerConnection";
import {intToBytes} from "./utils/binary";
import wake from "./utils/wake";
import {waitUntilTrue} from "./utils/async";
import MessageType from "./enums/MessageType";


type ReconnectionCallback = (authMessage: string) => void


export default class App extends EventsEmitter
{
    private _signalingServer : SignalingServer = null
    private _videoProcessor : VideoProcessor = null
    private _peerConfig: RTCConfiguration = null
    private _peerConnection: PeerConnection = null
    private _logIndex: number = 0

    private _userId: number = null
    private _authorized: boolean = false
    private _isAdmin: boolean = false

    private _isTalking: boolean = false

    private _currentVideoId: string = null
    private _lastVideoId: string = null

    private _logsHijacking: boolean = false

    constructor() {
        super()
        wake.enable().catch(e => log.error(e))
        this.prepareVideoProcessor()
        this.prepareSignalingServer()
    }

    private setUserId(id: number) {
        if (this._userId == null) this._userId = id
    }

    private prepareVideoProcessor() {
        this._videoProcessor = new VideoProcessor()

        this._videoProcessor.onRecordedChunkReady((data, videoId) => {
            let encoder = new TextEncoder()
            let id = encoder.encode(videoId)
            let len = intToBytes(id.length)
            let d = new Blob([len, id, data])
            this._signalingServer.sendBinary(d)
        })
    }

    private prepareSignalingServer() {
        this._signalingServer = new SignalingServer()

        let reconnectCallback: ReconnectionCallback = (authMessage: string) => {
            this._signalingServer.send({
                action: Action.AUTH,
                payload: authMessage
            })
        }

        this._signalingServer.onConnect(() => {
            this.dispatchEvent(AppEvent.CONNECTED, reconnectCallback)
            this.dispatchEvent(AppEvent.AUTH_REQUIRED, reconnectCallback)
        })

        this._signalingServer.onDisconnect(() => {
            this._authorized = false
            this.dispatchEvent(AppEvent.DISCONNECTED);
        })

        this._signalingServer.onMessage((msg: SignalingMessage) => {
            this.handleSignalingMessage(msg)
        })
    }


    private handleSignalingMessage(msg: SignalingMessage)
    {
        log.info("<<<", msg.action)

        if (msg.action === Action.CONFIGURATION) {
            if (msg.logs) this.hijackLogs()
            return;
        }

        if (msg.action === Action.COUNTRIES_LIST) {
            this.dispatchEvent(AppEvent.COUNTRIES_LIST, msg.countries)
            return;
        }

        if (msg.action === Action.VIDEO_SESSIONS_LIST) {
            this.dispatchEvent(AppEvent.VIDEO_SESSIONS_LIST, msg.videoSessions)
            return;
        }

        if (msg.action !== Action.AUTH && !this._authorized) return

        switch (msg.action)
        {
            case Action.AUTH:
                if (msg.payload.toLowerCase() === "ok") {
                    this._authorized = true
                    this._isAdmin = msg.isAdmin
                    this.dispatchEvent(AppEvent.AUTHORIZED, this._isAdmin)
                }
                break

            case Action.ACTIVE_VIDEO_SESSION:
                this.dispatchEvent(AppEvent.ACTIVE_VIDEO_SESSION, msg.videoSessionInfo)
                break

            case Action.VIDEO_SESSION_DETAILS:
                this.dispatchEvent(AppEvent.VIDEO_SESSION_DETAILS, msg.videoSessionInfo)
                break

            case Action.HIGHLIGHT_MEMBERS:
                this.dispatchEvent(AppEvent.HIGHLIGHT_MEMBERS, {
                    videoSessionId: msg.videoSessionId,
                    members: msg.members
                })
                break

            case Action.ERROR_MESSAGE:
                this.dispatchEvent(AppEvent.ERROR_MESSAGE, msg.message)
                break

            case Action.START_TALK:
                let newTalk = !this._isTalking
                this._isTalking = true
                let restartRecording = this._currentVideoId !== msg.videoId
                this._currentVideoId = msg.videoId
                this._lastVideoId = msg.videoId
                if (this._peerConnection === null || !newTalk) {
                    this.createPeerConnection()
                    if (this._userId === msg.initiator) {
                        this._peerConnection.initiateConnection().catch(e => log.error(e))
                    }
                } else if (this._userId === msg.initiator) {
                    if (restartRecording) {
                        this._videoProcessor.stopRecording()
                    }
                    this._peerConnection.reconnect()
                }
                this.dispatchEvent(AppEvent.START_TALK, this._userId === msg.initiator)
                break

            case Action.END_TALK:
                this._isTalking = false
                this._currentVideoId = null
                if (this._peerConnection !== null) {
                    this._peerConnection.disconnect()
                }
                this._peerConnection = null
                this._videoProcessor.stopRecording()
                this.dispatchEvent(AppEvent.END_TALK)
                break

            case Action.TALK_RESULT:
                this.dispatchEvent(AppEvent.TALK_RESULT, msg.talkResult)
                break;

            case Action.RTC_OFFER:
                if (this._peerConnection === null) {
                    this.createPeerConnection()
                }
                this._peerConnection.setRtcOffer(msg.sdp)
                break

            case Action.RTC_ANSWER:
                if (this._peerConnection === null) {
                    this.createPeerConnection()
                }
                this._peerConnection.setRtcAnswer(msg.sdp)
                break

            case Action.RTC_ICE:
                if (this._peerConnection === null) {
                    this.createPeerConnection()
                }
                this._peerConnection.setRemoteIce(msg.ice)
                break

            case Action.NOTIFICATIONS_LIST:
                this.dispatchEvent(AppEvent.NOTIFICATIONS_LIST, msg.notifications)
                break

            case Action.NEW_NOTIFICATION:
                this.dispatchEvent(AppEvent.NEW_NOTIFICATION, msg.notification)
                break

            case Action.CONVERSATIONS_LIST:
                this.dispatchEvent(AppEvent.CONVERSATIONS_LIST, msg.conversations)
                break

            case Action.INCOMING_MESSAGE:
                this.dispatchEvent(AppEvent.INCOMING_MESSAGE, {message: msg.message, conversationId: msg.conversationId})
                break

            case Action.MESSAGES_LIST:
                this.dispatchEvent(AppEvent.MESSAGES_LIST, {conversationId: msg.conversationId, messages: msg.messages})
                break

            case Action.CONVERSATION_UPDATE:
                this.dispatchEvent(AppEvent.CONVERSATION_UPDATE, msg.conversation)
                break

            default:
                log.warn("Unacceptable action: " + msg.action)
        }
    }

    public leaveTalk(comment: string) {
        this._signalingServer.send({
            action: Action.LEAVE_TALK,
            comment: comment
        })
    }


    private createPeerConnection() {
        this._peerConnection = new PeerConnection(this._signalingServer)
        if (this._peerConfig === null) {
            log.error("RTCConfiguration is not set")
        }
        this._peerConnection.configure(this._peerConfig)

        this._peerConnection.onLocalStreamReady((stream: MediaStream) => {
            this.dispatchEvent(AppEvent.LOCAL_VIDEO_STREAM_READY, stream)
        })
        this._peerConnection.onAllStreamsReady((localStream, remoteStream) => {
            if (this._peerConnection.isInitiator()) {
                this._videoProcessor.stopRecording()
                this._videoProcessor.startRecording(localStream, remoteStream, this._currentVideoId).catch(e => log.error(e))
            }
            this.dispatchEvent(AppEvent.REMOTE_VIDEO_STREAM_READY, remoteStream)
        })
    }


    /**
     * Initializes connection to the signaling server.
     * @param host Should be set in the next format: wss://date-assured.com:4000
     */
    public connectToSignalingServer(host: string)
    {
        this._signalingServer.setHost(host)
        this._signalingServer.connect()
    }

    public configureRTCPeer(rtcConfig: RTCConfiguration) {
        this._peerConfig = rtcConfig
        if (this._peerConnection !== null) {
            this._peerConnection.configure(rtcConfig)
        }
    }

    public initiateConnection() {
        this._peerConnection.initiateConnection()
    }

    public muteMicrophone() {
        this._peerConnection.mute()
    }

    public unmuteMicrophone() {
        this._peerConnection.unmute()
    }

    public isAuthorized(): boolean {
        return this._authorized
    }

    public createVideoSession(data: VideoSessionBaseInfo) {
        this._signalingServer.send({
            action: Action.CREATE_VIDEO_SESSION,
            videoSessionInfo: data
        })
    }

    public deleteVideoSession(id: number) {
        this._signalingServer.send({
            action: Action.DELETE_VIDEO_SESSION,
            videoSessionId: id
        })
    }

    public requestCountriesListUpdate()
    {
        this._signalingServer.send({
            action: Action.COUNTRIES_LIST
        })
    }

    public requestUserUpdate()
    {
        this._signalingServer.send({
            action: Action.UPDATE_USER
        })
    }

    public requestVideoSessionsListUpdate(country: string)
    {
        this._signalingServer.send({
            action: Action.VIDEO_SESSIONS_LIST,
            country: country
        })
    }

    public requestVideoSessionDetails(id: number)
    {
        this._signalingServer.send({
            action: Action.VIDEO_SESSION_DETAILS,
            videoSessionId: id
        })
    }

    public joinVideoSession(id: number)
    {
        this._signalingServer.send({
            action: Action.JOIN_VIDEO_SESSION,
            videoSessionId: id
        })
    }

    public leaveVideoSession(id: number)
    {
        this._signalingServer.send({
            action: Action.LEAVE_VIDEO_SESSION,
            videoSessionId: id
        })
    }

    public requestTalkResult()
    {
        this._signalingServer.send({
            action: Action.TALK_RESULT,
        })
    }

    public requestConversationsList()
    {
        this._signalingServer.send({
            action: Action.CONVERSATIONS_LIST
        })
    }

    public requestMessagesList(conversationId: number, fromId?: number)
    {
        this._signalingServer.send({
            action: Action.MESSAGES_LIST,
            conversationId: conversationId,
            fromId: fromId
        })
    }

    public sendMessage(text, conversationId) {
        this._signalingServer.send({
            action: Action.SEND_MESSAGE,
            conversationId: conversationId,
            type: MessageType.TEXT,
            text: text
        })
    }

    public seeMessage(conversationId: number, messageId: number) {
        this._signalingServer.send({
            action: Action.SEE_MESSAGE,
            conversationId: conversationId,
            messageId: messageId
        })
    }

    public onConnect(handler: () => void) {
        this.addEventHandler(AppEvent.CONNECTED, handler)
    }

    public onAuthorized(handler: (isAdmin: boolean) => void) {
        this.addEventHandler(AppEvent.AUTHORIZED, handler)
    }

    public onAuthRequired(handler: (callback: ReconnectionCallback) => void) {
        this.addEventHandler(AppEvent.AUTH_REQUIRED, handler)
    }

    public onActiveVideoSession(handler: (videoSessionInfo: VideoSessionInfo) => void) {
        this.addEventHandler(AppEvent.ACTIVE_VIDEO_SESSION, handler)
    }

    public onVideoStreamReady(handler: (stream: MediaStream) => void) {
        this.addEventHandler(AppEvent.VIDEO_STREAM_READY, handler)
    }

    public onLocalVideoStreamReady(handler: (stream: MediaStream) => void) {
        this.addEventHandler(AppEvent.LOCAL_VIDEO_STREAM_READY, handler)
    }

    public onRemoteVideoStreamReady(handler: (stream: MediaStream) => void) {
        this.addEventHandler(AppEvent.REMOTE_VIDEO_STREAM_READY, handler)
    }

    public onCountriesListUpdate(handler: (list: CountryInfo[]) => void) {
        this.addEventHandler(AppEvent.COUNTRIES_LIST, handler)
    }

    public onVideoSessionsListUpdate(handler: (list: VideoSessionInfo[]) => void) {
        this.addEventHandler(AppEvent.VIDEO_SESSIONS_LIST, handler)
    }

    public onVideoSessionDetails(handler: (info: VideoSessionInfo) => void) {
        this.addEventHandler(AppEvent.VIDEO_SESSION_DETAILS, handler)
    }

    public onHighlightMembers(handler: (info: HighlightInfo) => void) {
        this.addEventHandler(AppEvent.HIGHLIGHT_MEMBERS, handler)
    }

    public onErrorMessage(handler: (message: string) => void) {
        this.addEventHandler(AppEvent.ERROR_MESSAGE, handler)
    }

    public onStartTalk(handler: (isInitiator: boolean) => void) {
        this.addEventHandler(AppEvent.START_TALK, handler)
    }

    public onEndTalk(handler: () => void) {
        this.addEventHandler(AppEvent.END_TALK, handler)
    }

    public onTalkResult(handler: (talkResult: TalkResult) => void)
    {
        this.addEventHandler(AppEvent.TALK_RESULT, handler)
    }

    public onNotificationsUpdate(handler: (notifications: Notification[]) => void) {
        this.addEventHandler(AppEvent.NOTIFICATIONS_LIST, handler)
    }

    public onNewNotification(handler: (notification: Notification) => void) {
        this.addEventHandler(AppEvent.NEW_NOTIFICATION, handler)
    }

    public onConversationsListUpdate(handler: (conversations: Conversation[]) => void) {
        this.addEventHandler(AppEvent.CONVERSATIONS_LIST, handler)
    }

    public onConversationUpdate(handler: (conversation: Conversation) => void) {
        this.addEventHandler(AppEvent.CONVERSATION_UPDATE, handler)
    }

    public onIncomingMessage(handler: (data: {conversationId: number, message: Message}) => void) {
        this.addEventHandler(AppEvent.INCOMING_MESSAGE, handler)
    }

    public onMessagesUpdate(handler: (messagesData: {messages: Message[], conversationId: number}) => void) {
        this.addEventHandler(AppEvent.MESSAGES_LIST, handler)
    }

    public seeNotification(id: number) {
        this._signalingServer.send({
            action: Action.SEE_NOTIFICATION,
            id: id
        })
    }

    public rateLastTalk(rate: number) {
        this._signalingServer.send({
            action: Action.RATE_TALK,
            rate: rate
        })
    }

    public restartServer() {
        this._signalingServer.send({action: Action.RESTART_SERVER});
    }

    private async hijackLogs()
    {
        if (this._logsHijacking) return
        this._logsHijacking = true

        await waitUntilTrue(() => this._authorized)

        let log = console.log
        let warn = console.warn
        let error = console.error
        this.saveLogs(navigator.userAgent, "", 1)
        console.log = (...args) => {
            this.saveLogs("Log:   " + args.join(" "), "", 1)
            log.apply(console, args)
        };
        console.warn = (...args) => {
            this.saveLogs("Warn:  " + args.join(" "), "", 1)
            warn.apply(console, args)
        };
        console.error = (...args) => {
            this.saveLogs("Error: " + args.join(" "), "", 1)
            error.apply(console, args)
        };
        window.onerror = (msg, url, line) => {
            this.saveLogs(msg, url, line)
        }
    }

    public saveLogs(msg, url, line) {
        this._signalingServer.sendSilent({
            action: Action.LOGS,
            message: msg,
            file: url,
            line: line,
            index: this._logIndex++
        })
    }
}