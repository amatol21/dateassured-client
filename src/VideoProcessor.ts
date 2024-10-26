import log from "./utils/log";

export default class VideoProcessor
{
    private _recordedStream: MediaStream = null

    private readonly _canvas: HTMLCanvasElement = document.createElement('canvas')
    private readonly _localVideo: HTMLVideoElement = document.createElement('video')
    private readonly _remoteVideo: HTMLVideoElement = document.createElement('video')

    private _onRecordedChunkReady: (data: Blob, videId: string) => void = null

    private _recorder: MediaRecorder = null
    private _audioContext: AudioContext = null
    private _isRecording: boolean = false

    constructor() {
        this._localVideo.volume = 0
        this._remoteVideo.volume = 0
        this._remoteVideo.addEventListener('loadedmetadata', () => {
            this._remoteVideo.play().catch(e => log.error("Unable to play remote video:", e))
        })
    }

    public async startRecording(localStream: MediaStream, remoteStream: MediaStream, videoId: string)
    {
        let id = videoId
        this._isRecording = true
        this._recordedStream = await this.composeRecordedVideo(localStream, remoteStream)

        this._recorder = new MediaRecorder(this._recordedStream, {
            videoBitsPerSecond: 102400,
            mimeType: "video/webm; codecs=vp8,opus"
        })

        this._recorder.ondataavailable = event => {
            if (this._onRecordedChunkReady !== null) {
                this._onRecordedChunkReady(event.data, id)
            }
        }

        this._recorder.start(1000)
    }

    public stopRecording() {
        this._isRecording = false
        this._localVideo.srcObject = null
        this._remoteVideo.srcObject = null
        if (this._recorder !== null) {
            this._recorder.stop()
            this._recorder = null
        }
        if (this._audioContext !== null) {
            this._audioContext.close().catch(e => log.error(e))
            this._audioContext = null
        }
        if (this._recordedStream !== null) {
            this._recordedStream.getTracks().forEach(track => track.stop())
            this._recordedStream = null
        }
    }

    private composeRecordedVideo(localStream: MediaStream, remoteStream: MediaStream): Promise<MediaStream>
    {
        return new Promise(async resolve =>
        {
            let finalStream: MediaStream
            this._localVideo.srcObject = null
            this._remoteVideo.srcObject = null

            this._remoteVideo.addEventListener('loadedmetadata', () =>
            {
                let canvas: HTMLCanvasElement = this._canvas
                let ctx = canvas.getContext('2d')
                canvas.width = Math.floor(this._remoteVideo.videoWidth / 3)
                canvas.height = Math.floor(this._remoteVideo.videoHeight / 3)

                let loop = () => {
                    if (!this._localVideo.paused && !this._localVideo.ended) {
                        let scale = 0.35
                        let v = this._localVideo
                        let k = v.videoWidth - canvas.width > v.videoHeight - canvas.height
                            ? canvas.width/v.videoWidth
                            : canvas.height/v.videoHeight

                        let dw = Math.round(v.videoWidth * scale * k)
                        let dh = Math.round(v.videoHeight * scale * k)

                        if (this._remoteVideo !== null) {
                            let v = this._remoteVideo
                            let k = v.videoWidth - canvas.width > v.videoHeight - canvas.height
                                ? canvas.width/v.videoWidth
                                : canvas.height/v.videoHeight

                            ctx.drawImage(this._remoteVideo,
                                v.videoWidth - canvas.width > v.videoHeight - canvas.height ? 0 : canvas.width/2 - (v.videoWidth * k)/2,
                                v.videoWidth - canvas.width > v.videoHeight - canvas.height ? canvas.height/2 - (v.videoHeight * k)/2 : 0,
                                v.videoWidth * k,
                                v.videoHeight * k
                            )
                        } else {
                            ctx.fillStyle = '#000'
                            ctx.fillRect(0, 0, canvas.width, canvas.height)
                        }
                        ctx.drawImage(this._localVideo, canvas.width - dw, canvas.height - dh, dw, dh)
                        if (this._isRecording) {
                            setTimeout(loop, 1000 / 30)
                        }
                    }
                }
                loop()
                finalStream = canvas.captureStream(30)

                if (window['AudioContext'] !== undefined) {
                    this._audioContext = new AudioContext();
                    const source1 = this._audioContext.createMediaStreamSource(localStream)
                    const source2 = this._audioContext.createMediaStreamSource(remoteStream)
                    const destination = this._audioContext.createMediaStreamDestination()
                    source1.connect(destination)
                    source2.connect(destination)
                    if (destination.stream.getAudioTracks().length > 0) {
                        finalStream.addTrack(destination.stream.getAudioTracks()[0]);
                    }
                } else {
                    localStream.getAudioTracks().forEach(track => finalStream.addTrack(track))
                }

                resolve(finalStream)
            })

            let videoStream = new MediaStream()
            localStream.getVideoTracks().forEach(track => videoStream.addTrack(track))
            this._localVideo.srcObject = videoStream
            await this._localVideo.play()

            videoStream = new MediaStream()
            remoteStream.getVideoTracks().forEach(track => videoStream.addTrack(track))
            this._remoteVideo.srcObject = videoStream
            await this._remoteVideo.play()
        })
    }

    public onRecordedChunkReady(handler: (data: Blob, videoId: string) => void) {
        this._onRecordedChunkReady = handler
    }
}