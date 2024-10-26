<script src="/adapter.js"></script>
<script src="/video-sessions-manager.js"></script>
<script>
    (() => {
        try {
            let userId = null;
            let app = new VideoSessionsManager();

            // Provide some API for global access.
            window.restartVideoSessionsServer = () => app.restartServer();

            <?php if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) { ?>
            userId = <?=$_SESSION['id']?>;
            app.setUserId(userId);
            app.onAuthRequired(async callback => {
                let res = await fetch('/video-sessions/get-auth-message');
                if (res.ok) {
                    let authMsg = await res.json();
                    callback(authMsg);
                }
            })
            <?php } ?>

            let lastSeenVideoSessionId = null;

            app.configureRTCPeer({
                iceServers: [
                    { urls: 'stun:stun.l.google.com:19302' }
                ]
            });

            app.onActiveVideoSession(videoSessionInfo => {
                document.dispatchEvent(new CustomEvent('show-video-session-details', {
                    detail: videoSessionInfo
                }));
            });

            app.onVideoSessionsListUpdate(data => {
                document.dispatchEvent(new CustomEvent('video-sessions-list-update', {
                    detail: data
                }));
            });

            app.onVideoSessionDetails(data => {
                document.dispatchEvent(new CustomEvent('video-session-update', {
                    detail: data
                }));
            });

            app.onConnect(() => {
                app.requestVideoSessionsListUpdate();
            });

            app.onAuthorized(isAdmin => {
                window.userId = userId;
                if (lastSeenVideoSessionId !== null) {
                    app.requestVideoSessionDetails(lastSeenVideoSessionId);
                }
                document.dispatchEvent(new CustomEvent('authorized', {detail: isAdmin}));
                if (isAdmin) app.requestVideoSessionsListUpdate();
            });

            app.onHighlightMembers(data => {
                document.dispatchEvent(new CustomEvent('highlight-members', {
                    detail: data
                }));
            });

            app.onErrorMessage(message => {
                document.dispatchEvent(new CustomEvent('error-message', {
                    detail: message
                }));
            });

            app.onStartTalk(isInitiator => {
                document.dispatchEvent(new CustomEvent('start-talk', {
                    detail: isInitiator
                }));
            });

            app.onEndTalk(() => {
                document.dispatchEvent(new CustomEvent('end-talk'));
            });

            app.onLocalVideoStreamReady(stream => {
                document.dispatchEvent(new CustomEvent('local-video-stream-ready', {
                    detail: stream
                }));
            });

            app.onRemoteVideoStreamReady(stream => {
                document.dispatchEvent(new CustomEvent('remote-video-stream-ready', {
                    detail: stream
                }));
            });

            document.addEventListener('rate-talk', e => {
                app.rateLastTalk(e.detail);
            });

            document.addEventListener('vs-mute', e => {
                app.muteMicrophone();
            });

            document.addEventListener('vs-unmute', e => {
                app.unmuteMicrophone();
            });

            document.addEventListener('video-session-created', e => {
                app.createVideoSession(e.detail);
            });

            document.addEventListener('delete-video-session', e => {
                app.deleteVideoSession(e.detail);
            });

            document.addEventListener('request-video-sessions-list', () => {
                app.requestVideoSessionsListUpdate();
            });

            document.addEventListener('show-video-session-details', e => {
                lastSeenVideoSessionId = e.detail.id;
                app.requestVideoSessionDetails(e.detail.id);
            });

            document.addEventListener('join-video-session', e => {
                app.joinVideoSession(e.detail);
            });

            document.addEventListener('leave-video-session', e => {
                app.leaveVideoSession(e.detail);
            });

            document.addEventListener('leave-talk', e => {
                app.leaveTalk(e.detail);
            });

            app.connectToSignalingServer('ws://127.0.0.1:2245');
        } catch (e) {
            console.warn(e);
        }
    })();
</script>