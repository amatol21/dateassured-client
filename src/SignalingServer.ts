import log from "./utils/log";
import {SignalingMessage} from "./models";

export default class SignalingServer
{
    private _host: string
    private _ws: WebSocket = null
    private _queue: SignalingMessage[]
    private _messagesHandler: (msg: SignalingMessage) => void = null
    private _connectHandler: () => void = null
    private _disconnectHandler: () => void = null

    constructor() {
        this._queue = []
    }

    public setHost(host: string) {
        this._host = host
    }

    public connect()
    {
        log.info('Connecting to the signaling server')

        this._ws = new WebSocket(this._host)
        this._ws.onopen = () => {
            log.info('Successfully connected to the signaling server')
            if (this._connectHandler !== null) {
                this._connectHandler();
            }
        }

        this._ws.onmessage = (e: MessageEvent) => {
            if (this._messagesHandler !== null) {
                try {
                    this._messagesHandler(JSON.parse(e.data))
                } catch(e) {
                    log.error('Unable to parse incoming message: ', e)
                }
            }
        }

        this._ws.onclose = (e: CloseEvent) => {
            if (this._disconnectHandler !== null) {
                this._disconnectHandler()
            }
            setTimeout(() => {
                log.info('Connection to signaling server has been closed. Trying to reconnect...')
                this.connect()
            }, 1000)
        }
    }

    private processQueue() {
        for (let i = 0; i < this._queue.length; i++) {
            if (this._ws.readyState == WebSocket.OPEN) {
                this.sendInternal(this._queue[i])
            }
        }
        this._queue = []
    }

    public onConnect(handler: () => void) {
        this._connectHandler = handler
    }

    public onDisconnect(handler: () => void) {
        this._disconnectHandler = handler
    }

    public onMessage(handler: (msg: SignalingMessage) => void) {
        this._messagesHandler = handler
    }

    public sendBinary(data: Blob)
    {
        log.info('>>> [Binary data]', data.size)
        this._ws.send(data)
    }

    public send(msg: SignalingMessage)
    {
        if (this._ws === null || this._ws.readyState !== WebSocket.OPEN) {
            this._queue.push(msg)
            return
        }
        this.sendInternal(msg)
    }

    public sendSilent(msg: SignalingMessage) {
        if (this._ws !== null) this._ws.send(JSON.stringify(msg))
    }

    private sendInternal(msg: SignalingMessage)
    {
        log.info('>>>', msg.action)
        this._ws.send(JSON.stringify(msg))
    }
}