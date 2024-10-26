import log from "./utils/log";
import SignalingServer from "./SignalingServer";
import Action from "./enums/Action";
import {waitUntilTrue} from "./utils/async";


export default class PeerConnection
{
    private _signalingServer: SignalingServer
    private _connection: RTCPeerConnection = null

    private _localStream: MediaStream = null
    private _remoteStream: MediaStream = null
    private _initiatesConnection: boolean = false

    private _iceCandidates: RTCIceCandidate[] = []

    private _videoSender: RTCRtpSender = null
    private _audioSender: RTCRtpSender = null
    private _isMuted: boolean = false

    private _gettingLocalStream: boolean = false

    private _onLocalStreamReady: (stream: MediaStream) => void = null
    private _onAllStreamsReady: (localStream: MediaStream, remoteStream: MediaStream) => void = null

    constructor(signalingServer: SignalingServer) {
        this._signalingServer = signalingServer
        this.createLocalStream().catch(e => log.error(e))
    }

    public mute() {
        this._audioSender.track.enabled = false
    }

    public unmute() {
        this._audioSender.track.enabled = true
    }

    public isInitiator(): boolean {
        return this._initiatesConnection
    }

    public configure(rtcConfig: RTCConfiguration) {
        if (this._connection === null) {
            this._connection = new RTCPeerConnection(rtcConfig)

            this._connection.addEventListener("iceconnectionstatechange", () => {
                if (this._connection.iceConnectionState === "failed") {
                    this._connection.restartIce()
                }
            })

            this._connection.onnegotiationneeded  = async () => {
                log.info('Negotiation needed')
                if (!this._initiatesConnection) {
                    this._connection.restartIce()
                    return;
                }
                try {
                    let offer = await this._connection.createOffer()
                    await this._connection.setLocalDescription(offer)
                    this._signalingServer.send({
                        action: Action.RTC_OFFER,
                        sdp: offer.sdp
                    })
                } catch (reason) {
                    log.error("Unable to set local offer:", reason)
                }
            }

            this._connection.ontrack = (event: RTCTrackEvent) => {
                event.track.onunmute = () => {
                    if (event.track.kind === 'video') {
                        this._remoteStream = event.streams[0]
                        if (this._onAllStreamsReady !== null) {
                            this._onAllStreamsReady(this._localStream, this._remoteStream)
                        }
                    }
                }
            }

            this._connection.onicecandidate = (event: RTCPeerConnectionIceEvent) => {
                if (event.candidate) {
                    this._signalingServer.send({
                        action: Action.RTC_ICE,
                        ice: event.candidate
                    })
                }
            }
        } else {
            log.warn("Trying to configure peer connection when it's already configured.")
        }
    }

    public async initiateConnection() {
        this._initiatesConnection = true
        if (this._connection.connectionState === 'connected') {
            log.info("Connection already established")
            return
        }
        await this.createLocalStream()
    }

    public reconnect() {
        if (this._connection !== null) {
            log.info('Reconnecting...')
            this._connection.restartIce()
        }
    }

    public async setRtcAnswer(sdp: string) {
      try {
          await this._connection.setRemoteDescription({type: "answer", sdp: sdp})
          await this.setCachedIceCandidates()
      } catch (error) {
          log.error("Unable to set remote answer:", error)
      }
    }

    public async setRtcOffer(sdp: string) {
        await this.createLocalStream()
        try {
            await this._connection.setRemoteDescription({type: "offer", sdp: sdp})
            await this.setCachedIceCandidates()
            let answer = await this._connection.createAnswer()
            await this._connection.setLocalDescription(answer)
            this._signalingServer.send({
                action: Action.RTC_ANSWER,
                sdp: answer.sdp
            })
        } catch (error) {
            log.error("Unable to set local answer:", error)
        }
    }

    private createLocalStream() {
        return new Promise(async resolve => {
            if (this._localStream !== null) {
                resolve(this._localStream)
                return
            }

            if (this._gettingLocalStream) {
                await waitUntilTrue(() => this._gettingLocalStream === false)
                resolve(this._localStream)
                return
            }

            this._gettingLocalStream = true

            log.info('Created local stream')
            this._localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true })
            if (this._onLocalStreamReady !== null) {
                this._onLocalStreamReady(this._localStream)
            }
            if (this._connection === null) return resolve(this._localStream)
            this._localStream.getTracks().forEach(track => {
                let sender = this._connection.addTrack(track, this._localStream)
                if (track.kind === 'audio') {
                    if (this._isMuted) sender.track.enabled = false
                    this._audioSender = sender
                } else {
                    this._videoSender = sender
                }
            })
            this._gettingLocalStream = false
        })
    }

    public setRemoteIce(ice: RTCIceCandidate) {
        if (this._connection.remoteDescription === null) {
            this._iceCandidates.push(ice)
        } else {
            this._connection.addIceCandidate(ice).catch(reason => {
                log.error("Unable to set remote ice:", reason)
            })
        }
    }

    private async setCachedIceCandidates() {
        let promises = []
        for (let i = 0; i < this._iceCandidates.length; i++) {
            promises.push(this._connection.addIceCandidate(this._iceCandidates[i]))
        }
        await Promise.all(promises).catch(error => log.error("Unable to set remote ice:", error))
        this._iceCandidates = [];
    }

    public disconnect() {
        if (this._localStream !== null) {
            this._localStream.getTracks().forEach(track => track.stop())
        }
        this._localStream = null
        this._remoteStream = null
        this._connection.close()
    }

    public onLocalStreamReady(handler: (stream: MediaStream) => void) {
        this._onLocalStreamReady = handler
    }

    public onAllStreamsReady(handler: (localStream: MediaStream, remoteStream: MediaStream) => void) {
        this._onAllStreamsReady = handler
    }
}