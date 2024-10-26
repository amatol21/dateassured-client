<style>
    #vs-modal-wrap {
        display: flex;
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        z-index: 10000;
        background-color: #00000066;
        overscroll-behavior: contain;
        overflow-y: auto;
        overflow-x: hidden;
    }
    #vs-modal {
        display: flex;
        flex-direction: column;
        margin: auto;
        position: relative;
        width: 50rem;
        top: -5rem;
        max-width: calc(100% - 2rem);
        min-height: 40rem;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 3px rgba(0, 0, 0, 0.028), 0 5px 9px rgba(0, 0, 0, 0.042), 0 20px 40px rgba(0, 0, 0, 0.07);
        z-index: 101;
        transition: top 0.25s;
    }
    #vs-modal.shown {
        top: 0;
    }
    #vs-modal__close-button {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 2rem;
        height: 2rem;
        position: absolute;
        right: 1rem;
        top: 1rem;
        border-radius: 1rem;
        font-size: 2rem;
        opacity: 0.5;
        cursor: pointer;
        background-color: transparent;
        transition: opacity 150ms;
    }
    #vs-modal__close-button:hover {
        opacity: 1;
    }
    .vs__team {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }
    .vs__team__member-skeleton {
        width: 6rem;
        height: 6rem;
        margin: 0.5rem;
        position: relative;
        overflow: hidden;
        background-color: #DDDBDD;
        border-radius: 5rem;
    }
    .vs__team__member-skeleton::after {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        transform: translateX(-100%);
        background-image: linear-gradient(90deg, #ffffff00, #ffffff33, #ffffff99, #ffffff33, #ffffff00);
        animation: shimmer 2s infinite;
        content: '';
    }
    .vs__team__member {
        width: 6rem;
        height: 6rem;
        margin: 0.5rem;
        position: relative;
        overflow: visible;
        background-color: #DDDBDD;
        border-radius: 5rem;
        border: 1px solid #999;
        /*box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04), 0 2px 2px rgba(0, 0, 0, 0.06), 0 10px 10px rgba(0, 0, 0, 0.1);*/
    }
    .vs-team-member_highlighted.gender-male {
        outline: 5px solid rgba(52, 146, 222, 0.33);
    }
    .vs-team-member_highlighted.gender-female {
        outline: 5px solid #de345755;
    }
    .vs__team__member-photo {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 3rem;
    }
    .vs__team__member__age {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 2rem;
        height: 2rem;
        border-radius: 2rem;
        position: absolute;
        right: 0;
        bottom: 0;
        z-index: 100;
        background-color: #fff;
        text-align: center;
        font-size: 1rem;
        border: 1px solid #999;
    }
    .vs__team__member__age::after {
        content: 'y';
        font-size: 0.75rem;
        margin-left: 1px;
    }
    .vs__team__no-member {
        border: 1px solid #999;
    }
    .vs__team__no-member .vs__team__member__age {
        display: none;
    }
    #vs-modal__header {
        min-height: 5rem;
        flex-grow: 0;
        padding: 1rem 6rem 0.75rem 4rem;
        background-color: #eee;
        border-bottom: 1px solid #ddd;
        border-radius: 8px 8px 0 0;
    }
    .vs__team__title {
        margin-top: 2rem;
        font-size: 1.25rem;
        color: #555;
        text-align: center;
    }
    #vs__teams-wrap {
        margin-bottom: 2rem;
    }
    #vs-modal__buttons {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 2rem;
        flex-shrink: 0;
    }
    #vs-join-button {
        background-color: #0087e8;
        padding: 0.5rem 2rem;
        border-radius: 2rem;
        color: #fff;
        cursor: pointer;
        transition: background-color 150ms;
    }
    #vs-join-button:hover {
        background-color: #0379ff;
    }
    #vs-leave-button {
        background-color: #d20000;
        padding: 0.5rem 2rem;
        border-radius: 2rem;
        color: #fff;
        cursor: pointer;
        transition: background-color 150ms;
    }
    #vs-leave-button:hover {
        background-color: #ff0000;
    }
    #vs-timer {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        line-height: 1.25;
        height: 6rem;
    }
    #vs-timer-time {
        font-size: 2rem;
        margin-top: 2rem;
        font-weight: 600;
        color: #333;
    }
    #vs-timer-status {
        font-size: 0.85rem;
        color: #999;
    }
    #vs-video-wrap {
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        max-width: 100%;
        max-height: 100%;
        background-color: #000000;
        border-radius: 8px;
        padding: 1rem;
        z-index: 100;
    }
    #vs-video-remote {
        max-width: 100%;
        max-height: 100%;
        position: relative;
    }
    #vs-video-local {
        position: absolute;
        z-index: 10;
        right: 2rem;
        bottom: 2rem;
        max-width: 30%;
        max-height: 30%;
        border-radius: 8px;
        background-color: #000;
    }
    #vs-video-timer {
        position: absolute;
        top: 2rem;
        left: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 8rem;
        height: 2rem;
        margin-left: -4rem;
        font-size: 1.25rem;
        font-weight: 600;
        color: #000;
        background-color: #fff;
        border-radius: 2rem;
        z-index: 10;
    }
    #vs-video-mute-button, #vs-video-unmute-button {
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        left: 2rem;
        bottom: 2rem;
        width: 3rem;
        height: 3rem;
        border-radius: 2rem;
        background-color: #ffffff;
        cursor: pointer;
        z-index: 10;
    }
    #vs-video-mute-button:hover, #vs-video-unmute-button:hover {
        background-color: #eeeeee;
    }
    #vs-video-mute-button > img, #vs-video-unmute-button > img {
        max-width: 1.5rem;
        max-height: 1.5rem;
    }
    #vs-video-leave-button {
        position: absolute;
    }
    #vs-video-fullscreen-button, #vs-video-exit-fullscreen-button {
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        left: 6rem;
        bottom: 2rem;
        width: 3rem;
        height: 3rem;
        border-radius: 2rem;
        background-color: #ffffff;
        cursor: pointer;
        z-index: 10;
    }
    #vs-video-fullscreen-button:hover, #vs-video-exit-fullscreen-button:hover {
        background-color: #eeeeee;
    }
    #vs-video-fullscreen-button > img, #vs-video-exit-fullscreen-button > img {
        max-width: 1.5rem;
        max-height: 1.5rem;
    }
    #vs-video-leave-button {
        position: absolute;
        height: 3rem;
        background-color: #c90e0e;
        color: #fff;
        padding: 0 1.5rem;
        border-radius: 2rem;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        text-transform: UPPERCASE;
        bottom: 2rem;
        left: 10rem;
        cursor: pointer;
        user-select: none;
    }
    #vs-video-leave-button:hover {
        background-color: #ee0808;
    }
    #vs-video-username {
        color: #fff;
        text-shadow: 0 0 5px BLACK;
        font-size: 1.25rem;
        position: absolute;
        top: 4.5rem;
        left: 50%;
        z-index: 10;
        width: 14rem;
        margin-left: -7rem;
        text-align: center;
    }
    #vs-rate-wrap {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        z-index: 100;
        background-color: #fff;
        border-radius: 1rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    #vs-rate-title {
        font-size: 2rem;
        margin-bottom: 1rem;
    }
    #vs-rate-buttons {
        display: flex;
    }
    #vs-like-button, #vs-dislike-button {
        position: relative;
        width: 4rem;
        height: 4rem;
        padding: 0.25rem;
        margin: 0.5rem;
        border: 1px solid transparent;
        border-radius: 0.5rem;
        transform: scale(1);
        opacity: 1;
        transition: transform 150ms, margin-left 500ms 500ms, margin-right 500ms 500ms, opacity 500ms 500ms;
        cursor: pointer;
    }
    #vs-like-button::before, #vs-dislike-button::before {
        content: '';
        width: 0;
        height: 0;
        position: absolute;
        left: 2rem;
        top: 2rem;
        border-radius: 5rem;
        transition: 500ms;
    }
    #vs-like-button::before {
        border: 2px solid #43a33d;
    }
    #vs-dislike-button::before {
        border: 2px solid #fe0033;
    }
    #vs-like-button.active::before, #vs-dislike-button.active::before {
        width: 6rem;
        height: 6rem;
        left: -1rem;
        top: -1rem;
        border-color: transparent;
    }
    #vs-like-button:hover, #vs-dislike-button:hover {
        transform: scale(1.15);
    }
    #vs-like-button.active, #vs-dislike-button.active {
        transform: scale(1.45);
        transition: transform 0ms;
        animation: rateButtonAnim;
        animation-duration: 250ms;
        animation-direction: reverse;
    }
    #vs-like-button.inactive {
        pointer-events: none;
        margin-left: -4rem;
        opacity: 0;
    }
    #vs-dislike-button.inactive {
        pointer-events: none;
        margin-right: -4rem;
        opacity: 0;
    }
    #vs-like-button img, #vs-dislike-button img {
        max-width: 100%;
        max-height: 100%;
        position: relative;
        z-index: 5;
    }
    #vs-rate-skip-button {
        border: 2px solid #ddd;
        padding: 0.5rem 2rem;
        margin-top: 1rem;
        border-radius: 0.5rem;
        cursor: pointer;
        user-select: none;
        transition: border-color 150ms, opacity 500ms 500ms;
        opacity: 1;
    }
    #vs-rate-skip-button:hover {
        border-color: #ccc;
    }
    #vs-rate-skip-button.inactive {
        pointer-events: none;
        opacity: 0;
    }

    @keyframes rateButtonAnim {
        0% {
            transform: scale(1.4);
        }
        40% {
            transform: scale(1.4);
        }
        70% {
            transform: scale(1.55);
        }
        100% {
            transform: scale(1.15);
        }
    }

    @media (max-width: 600px) {
        #vs-modal-wrap {
            padding: 1rem 0;
        }
        #vs-video-local {
            bottom: 6rem;
            right: 1rem;
        }
        #vs-modal__header {
            min-height: 5rem;
            flex-grow: 0;
            padding: 1rem 3rem 0.75rem 1rem;
        }
        #vs-video-timer {
            position: fixed;
            left: 50%;
            top: 1rem;
            margin-left: -4rem;
        }
        #vs-rate-title {
            font-size: 1.5rem;
        }
    }
</style>


<div id="vs-modal-wrap" style="display: none">
    <div id="vs-modal">
        <div id="vs-modal__close-button">
            <img src="/assets/images/icons/cross.svg" alt="Close" width="12" height="auto">
        </div>
        <div id="vs-modal__header"></div>

        <div id="vs-video-wrap" style="display: none; opacity: 0">
            <div id="vs-video-timer"></div>
            <div id="vs-video-username"></div>
            <div id="vs-video-leave-button">Leave session</div>
            <div id="vs-video-mute-button">
                <img src="/assets/images/icons/unmute.svg" alt="Mute">
            </div>
            <div id="vs-video-unmute-button" style="display: none">
                <img src="/assets/images/icons/mute.svg" alt="Unmute">
            </div>
            <div id="vs-video-fullscreen-button">
                <img src="/assets/images/icons/fullscreen.svg" alt="Fullscreen">
            </div>
            <div id="vs-video-exit-fullscreen-button" style="display: none">
                <img src="/assets/images/icons/exit-fullscreen.svg" alt="Exit fullscreen">
            </div>
            <video id="vs-video-remote" playsinline autoplay></video>
            <video id="vs-video-local" playsinline autoplay muted></video>
        </div>

<!--        <div id="vs-rate-wrap" style="display: none; opacity: 0">-->
        <div id="vs-rate-wrap">
            <div id="vs-rate-title">How was <span></span>?</div>
            <div id="vs-rate-buttons">
                <div id="vs-like-button">
                    <img src="/assets/images/icons/like.svg" alt="Like">
                </div>
                <div id="vs-dislike-button">
                    <img src="/assets/images/icons/dislike.svg" alt="Dislike">
                </div>
            </div>
            <div id="vs-rate-skip-button">Skip</div>
        </div>

        <div id="vs__teams-wrap" style="opacity: 0">
            <div class="vs__team__title">Male</div>
            <div id="vs__team-male" class="vs__team"></div>

            <div id="vs-timer">
                <div id="vs-timer-time"></div>
                <div id="vs-timer-status">Waiting for start</div>
            </div>

            <div class="vs__team__title">Female</div>
            <div id="vs__team-female" class="vs__team"></div>
        </div>

        <div id="vs-modal__buttons">
            <div id="vs-join-button" style="display: none; opacity: 0">Join</div>
            <div id="vs-leave-button" style="display: none; opacity: 0">Leave</div>
        </div>
    </div>
</div>

<script>
    (() => {
        let wrap = document.getElementById('vs-modal-wrap');
        let modal = document.getElementById('vs-modal');
        let header = document.getElementById('vs-modal__header');
        let closeButton = document.getElementById('vs-modal__close-button');
        let teamsWrap = document.getElementById('vs__teams-wrap');
        let teamMale = document.getElementById('vs__team-male');
        let teamFemale = document.getElementById('vs__team-female');
        let buttonsWrap = document.getElementById('vs-modal__buttons');
        let joinButton = document.getElementById('vs-join-button');
        let leaveButton = document.getElementById('vs-leave-button');
        let timer = document.getElementById('vs-timer');
        let timerTime = document.getElementById('vs-timer-time');
        let timerStatus = document.getElementById('vs-timer-status');
        let videoWrap = document.getElementById('vs-video-wrap');
        let remoteVideo = document.getElementById('vs-video-remote');
        let localVideo = document.getElementById('vs-video-local');
        let videoTimer = document.getElementById('vs-video-timer');
        let muteButton = document.getElementById('vs-video-mute-button');
        let unmuteButton = document.getElementById('vs-video-unmute-button');
        let fullscreenButton = document.getElementById('vs-video-fullscreen-button');
        let exitFullscreenButton = document.getElementById('vs-video-exit-fullscreen-button');
        let username = document.getElementById('vs-video-username');
        let leaveTalkButton = document.getElementById('vs-video-leave-button');
        let rateWrap = document.getElementById('vs-rate-wrap');
        let rateTitle = document.getElementById('vs-rate-title');
        let likeButton = document.getElementById('vs-like-button');
        let dislikeButton = document.getElementById('vs-dislike-button');
        let skipRateButton = document.getElementById('vs-rate-skip-button')

        let rateButtonsHiding = false;
        let isFullscreen = false;

        likeButton.addEventListener('click', () => {
            likeButton.classList.add('active');
            dislikeButton.classList.add('inactive');
            skipRateButton.classList.add('inactive');
            setTimeout(() => {
                likeButton.classList.remove('active');
                dislikeButton.classList.remove('inactive');
                skipRateButton.classList.remove('inactive');
            }, 5000);
            setTimeout(hideRateButtons, 1000);
            document.dispatchEvent(new CustomEvent('rate-talk', {detail: 1}));
        });

        dislikeButton.addEventListener('click', () => {
            dislikeButton.classList.add('active');
            likeButton.classList.add('inactive');
            skipRateButton.classList.add('inactive');
            setTimeout(() => {
                dislikeButton.classList.remove('active');
                likeButton.classList.remove('inactive');
                skipRateButton.classList.remove('inactive');
            }, 5000);
            setTimeout(hideRateButtons, 1000);
            document.dispatchEvent(new CustomEvent('rate-talk', {detail: -1}));
        });

        function hideRateButtons() {
            if (rateWrap.style.display === 'none') return;
            if (rateButtonsHiding) return;
            rateButtonsHiding = true;
            fadeOut(rateWrap, 500).then(() => {
                Promise.all([fadeIn(teamsWrap, 500), fadeIn(buttonsWrap, 500)]).then(() => {
                    rateButtonsHiding = false;
                    if (vs !== null && vs.status === 3) {
                        setTimeout(closeModal, 2000);
                    }
                })
            });
        }

        skipRateButton.addEventListener('click', () => {
            hideRateButtons();
        })

        localVideo.addEventListener('load', () => {
            localVideo.play();
        });
        remoteVideo.addEventListener('load', () => {
            remoteVideo.play();
        });

        remoteVideo.addEventListener('loadedmetadata', () => {
            remoteVideo.style.width = remoteVideo.videoWidth > remoteVideo.videoHeight ? '100%' : 'auto';
            remoteVideo.style.height = remoteVideo.videoWidth > remoteVideo.videoHeight ? 'auto' : '100%';
        });

        let countdown = new Countdown();
        countdown.onUpdate(timerString => {
            timerTime.textContent = timerString;
            videoTimer.textContent = timerString;
        });

        let modalShown = false;
        let vs = null;
        let isTalkingNow = false;

        async function closeModal() {
            modal.classList.remove('shown');
            fadeOut(teamsWrap, 250, false);
            await fadeOut(wrap)
            enableBodyScrollbar();
            modalShown = false;
        }

        wrap.addEventListener('click', () => {
            if (vs.status !== 1) closeModal()
        });
        closeButton.addEventListener('click', closeModal);
        modal.addEventListener('click', e => e.stopPropagation());

        document.addEventListener('show-video-session-details', async e => {
            modalShown = true;
            vs = e.detail;
            clearHighlights();
            updateDetails();
            requestAnimationFrame(() => modal.classList.add('shown'));
            disableBodyScrollbar();
            videoWrap.style.display = 'none';
            videoWrap.style.opacity = '0';
            joinButton.style.display = 'none';
            joinButton.style.opacity = '0';
            leaveButton.style.display = 'none';
            leaveButton.style.opacity = '0';
            rateWrap.style.display = 'none';
            rateWrap.style.opacity = '0';
            await fadeIn(wrap);
            if (!isTalkingNow) await fadeIn(teamsWrap, 500);
        });

        document.addEventListener('video-session-update', e => {
            let data = e.detail;
            if (!modalShown || data.id !== vs.id) return;
            vs = data;
            updateDetails();
        });

        document.addEventListener('highlight-members', e => {
            if (vs === null || vs.id !== e.detail.videoSessionId) return;
            highlightMembers(e.detail.members);
        });

        document.addEventListener('error-message', e => {
            alert(e.detail);
        });

        document.addEventListener('start-talk', () => {
            hideRateButtons();
            setTimeout(() => {
                isTalkingNow = true;
                requestAnimationFrame(() => {
                    Promise.all([
                        fadeOut(teamsWrap, 250, false),
                        fadeOut(buttonsWrap, 250, false)
                    ]).then(() => {
                        fadeIn(videoWrap, 250).then(() => {
                            if (window.innerWidth < 700) {
                                fullscreen();
                            }
                        });
                    });
                });
            }, 1000)
        });

        muteButton.addEventListener('click', async () => {
             muteButton.style.display = 'none';
             unmuteButton.style.display = null;
             document.dispatchEvent(new CustomEvent('vs-mute'));
        });

        unmuteButton.addEventListener('click', async () => {
            unmuteButton.style.display = 'none';
            muteButton.style.display = null;
            document.dispatchEvent(new CustomEvent('vs-unmute'));
        });

        fullscreenButton.addEventListener('click', async () => {
            fullscreenButton.style.display = 'none';
            exitFullscreenButton.style.display = null;
            fullscreen();
        });

        exitFullscreenButton.addEventListener('click', async () => {
            exitFullscreenButton.style.display = 'none';
            fullscreenButton.style.display = null;
            exitFullscreen();
        });

        async function fullscreen() {
            if (isFullscreen) return;
            isFullscreen = true;
            let rect = modal.getBoundingClientRect();
            videoWrap.style.left = rect.left + 'px';
            videoWrap.style.top = rect.top + 'px';
            videoWrap.style.width = rect.width + 'px';
            videoWrap.style.height = rect.height + 'px';
            videoWrap.style.position = 'fixed';
            await animate(k => {
                videoWrap.style.left = rect.left - (rect.left * k) + 'px';
                videoWrap.style.top = rect.top - (rect.top * k) + 'px';
                videoWrap.style.width = rect.width + ((window.innerWidth - rect.width) * k) + 'px';
                videoWrap.style.height = rect.height + ((window.innerHeight - rect.height) * k) + 'px';
            }, 150);
            videoWrap.style.borderRadius = '0';
        }

        async function exitFullscreen() {
            if (!isFullscreen) return;
            isFullscreen = false;
            let rect = modal.getBoundingClientRect()
            await animate(k => {
                videoWrap.style.left = (rect.left * k) + 'px';
                videoWrap.style.top = (rect.top * k) + 'px';
                videoWrap.style.width = window.innerWidth - ((window.innerWidth - rect.width) * k) + 'px';
                videoWrap.style.height = window.innerHeight - ((window.innerHeight - rect.height) * k) + 'px';
            }, 150);
            videoWrap.style.borderRadius = null;
            videoWrap.style.left = '0';
            videoWrap.style.top = '0';
            videoWrap.style.width = null;
            videoWrap.style.height = null;
            videoWrap.style.position = 'absolute';
        }

        document.addEventListener('end-talk', () => {
            isTalkingNow = false;
            rateTitle.querySelector('span').textContent = username.textContent;
            exitFullscreen();
            fadeOut(videoWrap, 500, true).then(() => {
                fadeIn(rateWrap, 500);
                setTimeout(hideRateButtons, 5000);
            });
        });

        document.addEventListener('local-video-stream-ready', e => {
            localVideo.srcObject = null;
            localVideo.srcObject = e.detail
        });

        document.addEventListener('remote-video-stream-ready', e => {
            remoteVideo.srcObject = null;
            remoteVideo.srcObject = e.detail
        });

        joinButton.addEventListener('click', () => {
            document.dispatchEvent(new CustomEvent('join-video-session', {detail: vs.id}));
        });

        leaveButton.addEventListener('click', () => {
            document.dispatchEvent(new CustomEvent('leave-video-session', {detail: vs.id}));
        });

        leaveTalkButton.addEventListener('click', () => {
            if (confirm('Are you really want to leave talk?')) {
                document.dispatchEvent(new CustomEvent('leave-talk', {
                    detail: prompt('Tell us why you left talk:', '')
                }));
            }
        });

        function clearHighlights() {
            for (let i = 0; i < teamMale.childNodes.length; i++) {
                teamMale.childNodes[i].classList.remove('vs-team-member_highlighted');
            }
            for (let i = 0; i < teamFemale.childNodes.length; i++) {
                teamFemale.childNodes[i].classList.remove('vs-team-member_highlighted');
            }
        }

        function highlightMembers(members) {
            if (members.length === 0) {
                clearHighlights();
                return;
            }
            members.map(position => {
                let cont = teamMale;
                let arr = vs.members.male
                if (position >= vs.teamSize) {
                    position -= vs.teamSize;
                    cont = teamFemale;
                    arr = vs.members.female
                }
                if (arr[position].id !== window.userId) {
                    username.textContent = arr[position].username;
                }
                if (position >= cont.childNodes.length) return;
                cont.childNodes[position].classList.add('vs-team-member_highlighted');
            });
        }

        function fillSkeletons(container) {
            container.innerHTML = '';
            let html = '';
            for (let i = 0; i < vs.teamSize; i++) {
                html += '<div class="vs__team__member-skeleton"></div>';
            }
            container.innerHTML = html;
        }

        function updateMembers(container, members) {
            for (let i = 0; i < members.length; i++) {
                skeletonToMember(container.childNodes[i], members[i]);
            }
        }

        function memberToSkeleton(el) {

        }

        function avatarErrorHandler() {
            this.src = '/uploads/avatars/' + (this.__gender === 'MALE' ? 'male' : 'female') + '-default.png';
        }

        async function skeletonToMember(oldElement, member) {
            if (oldElement.classList.contains('vs__team__member')) {
                let isHighlighted = oldElement.classList.contains('vs-team-member_highlighted');
                oldElement.className = 'vs__team__member';
                if (isHighlighted) oldElement.classList.add('vs-team-member_highlighted');
                oldElement.classList.add(member === false ? 'vs__team__no-member' : 'vs__team__real-member');
                if (member !== false) {
                    oldElement.classList.add('gender-' + member.gender.toLowerCase());
                }
                oldElement.__photo.src = member === false
                    ? '/assets/images/no-member.png'
                    : (member.photoUrl === '' ? '/assets/images/' + (member.gender === 'MALE' ? 'male' : 'female') + '-default.png' : member.photoUrl);
                oldElement.__photo.__gender = member === false ? null : member.gender;
                oldElement.__photo.alt = member === false ? 'Free place' : member.username;
                oldElement.__age.textContent = member.age;
                return;
            }

            let wrap = document.createElement('div');
            wrap.className = 'vs__team__member';
            wrap.classList.add(member === false ? 'vs__team__no-member' : 'vs__team__real-member');
            if (member !== false) {
                wrap.classList.add('gender-' + member.gender.toLowerCase());
            }

            let photo = document.createElement('img');
            photo.className = 'vs__team__member-photo';
            photo.alt = member === false ? 'Free place' : member.username;
            photo.onerror = photo.src = '/assets/images/' + (member.gender === 'MALE' ? 'male' : 'female') + '-default.png';
            photo.src = member === false ? '/assets/images/no-member.png' : (member.photoUrl === '' ? '/assets/images/' + (member.gender === 'MALE' ? 'male' : 'female') + '-default.png' : member.photoUrl);
            photo.__gender = member === false ? null : member.gender;
            photo.onerror = avatarErrorHandler;
            wrap.appendChild(photo);
            wrap.__photo = photo;

            let age = document.createElement('div');
            age.className = 'vs__team__member__age';
            age.textContent = member === false ? '' : member.age;
            wrap.appendChild(age);
            wrap.__age = age;

            if (oldElement.classList.contains('vs__team__member-skeleton')) {
                await fadeOut(oldElement, 250, false);
                wrap.style.opacity = '0';
                if (oldElement.parentNode !== null)
                    oldElement.parentNode.replaceChild(wrap, oldElement);
                await fadeIn(wrap);
            } else {
                if (oldElement.parentNode !== null)
                    oldElement.parentNode.replaceChild(wrap, oldElement);
            }
        }

        function isMember() {
            if (!vs.hasOwnProperty('members')) return false;
            for (let i = 0; i < vs.members.male.length; i++) {
                if (vs.members.male[i] !== false && vs.members.male[i].id === window.userId) return true;
            }
            for (let i = 0; i < vs.members.female.length; i++) {
                if (vs.members.female[i] !== false && vs.members.female[i].id === window.userId) return true;
            }
            return false;
        }

        function updateDetails() {
            if (vs === null) return;

            // Create header
            let info = createSessionInfo(vs);
            header.innerHTML = '';
            header.appendChild(info);

            countdown.setTargetTime(vs.startedAt);

            let msg = 'Waiting for start';
            if (vs.status === 1) {
                msg = 'Talking with member';
            } else if (vs.status === 2) {
                msg = 'Waiting for next conversation';
            } else if (vs.status === 3) {
                msg = 'Video session is completed';
            }
            timerStatus.textContent = msg;

            // Show members skeletons
            if (teamMale.childNodes.length !== vs.teamSize
                || teamMale.firstChild.classList.contains('vs__team__member-skeleton')) {
                fillSkeletons(teamMale);
                fillSkeletons(teamFemale);
            }

            // Replace skeletons with real members
            if (vs.hasOwnProperty('members')) {
                updateMembers(teamMale, vs.members.male);
                updateMembers(teamFemale, vs.members.female);

                if (vs.status === 0 && window.userId !== null) {
                    (async () => {
                        if (isMember()) {
                            await fadeOut(joinButton, 100);
                            await fadeIn(leaveButton, 1000);
                        } else {
                            await fadeOut(leaveButton, 100);
                            await fadeIn(joinButton, 1000);
                        }
                    })();
                } else {
                    joinButton.style.display = 'none';
                    joinButton.style.opacity = '0';
                    leaveButton.style.display = 'none';
                    leaveButton.style.opacity = '0';
                }
            }
        }
    })();
</script>