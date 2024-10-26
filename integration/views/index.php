<?= view('video-sessions/session-modal') ?>

<style>
    #vs-list, #vs-list-skeleton {
        display: flex;
        flex-direction: column;
    }

    .vs-list-item {
        display: flex;
        align-items: flex-start;
        width: 100%;
        min-height: 4rem;
        margin-bottom: 0.5rem;
        border-radius: 8px;
        background-color: #f9f9f9;
        padding: 0 1.5rem;
        cursor: pointer;
        border: 2px solid #ddd;
        transition: border 250ms;
    }
    .vs-list-item:hover {
        border: 2px solid #999;
    }
    .vs-list-item__icon-wrap {
        display: flex;
        flex-shrink: 0;
        width: 2.5rem;
        height: 2.5rem;
        justify-content: center;
        align-items: center;
    }
    .vs-list-item__icon {
        max-width: 100%;
        max-height: 100%;
    }
    .vs-list-item__info {
        display: flex;
        align-items: center;
        flex-grow: 1;
    }
    .vs-list-item__age,
    .vs-list-item__purpose,
    .vs-list-item__location {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        position: relative;
        padding-right: 1rem;
        margin-right: 1rem;
        height: 4rem;
    }
    .vs-list-item__age::after,
    .vs-list-item__purpose::after,
    .vs-list-item__location::after {
        content: '';
        display: block;
        width: 0;
        height: 2rem;
        position: absolute;
        left: 100%;
        top: 1rem;
        border-right: 1px solid #ccc;
    }
    .vs-list-item__age {
        width: 5rem;
        margin-left: 1rem;
    }
    .vs-list-item__age-label {
        color: #999;
        font-size: 0.75rem;
    }
    .vs-list-item__age-text {
        color: #333;
        font-size: 1.1rem;
    }
    .vs-list-item__purpose {
        width: 10rem;
    }
    .vs-list-item__purpose-label {
        color: #999;
        font-size: 0.75rem;
    }
    .vs-list-item__purpose-text {
        color: #333;
        font-size: 1.1rem;
    }
    .vs-list-item__location {
        flex-grow: 1;
    }
    .vs-list-item__location-label {
        color: #999;
        font-size: 0.75rem;
    }
    .vs-list-item__location-text {
        color: #333;
        font-size: 1.1rem;
    }
    .vs-list-item__date-wrap {
        display: flex;
        flex-direction: column;
        width: 5rem;
        line-height: 1.15;
    }
    .vs-list-item__date {
        color: #777;
        font-size: 0.85rem;
    }
    .vs-list-item__time {
        color: #555;
        font-size: 1.75rem;
        font-weight: 600;
    }
    .vs-list-skeleton-item {
        width: 100%;
        height: 4rem;
        margin-bottom: 0.5rem;
        position: relative;
        overflow: hidden;
        background-color: #DDDBDD;
        border-radius: 8px;
    }
    .vs-list-skeleton-item::after {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        transform: translateX(-100%);
        background-image: linear-gradient(90deg, #ffffff00, #ffffff33, #ffffff99, #ffffff00);
        animation: shimmer 2s infinite;
        content: '';
    }
    #no-vs-msg {
        width: 32rem;
        max-width: 100%;
        margin: auto;
        padding: 2rem;
        background-color: #eee;
        border-radius: 8px;
    }
    .no-vs-msg__title {
        font-size: 1.25rem;
        color: #333;
        font-weight: 600;
        margin-bottom: 0.25rem;
        text-align: center;
    }
    .no-vs-msg__description {
        font-size: 0.9rem;
        color: #666;
        text-align: center;
    }
    .vs-list-item__delete-button {
        margin-left: 1rem;
        width: 2rem;
        height: 2rem;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 1.5rem;
        color: #999;
        cursor: pointer;
    }
    .vs-list-item__delete-button:hover {
        color: #000;
    }
    @media (max-width: 600px) {
        .vs-list-item__purpose,
        .vs-list-item__location {
            display: none;
        }
    }
</style>


<div id="vs-list-skeleton">
    <div class="vs-list-skeleton-item"></div>
    <div class="vs-list-skeleton-item"></div>
    <div class="vs-list-skeleton-item"></div>
    <div class="vs-list-skeleton-item"></div>
</div>

<div id="vs-list" style="display: none; opacity: 0;"></div>

<div id="no-vs-msg" style="display: none; opacity: 0">
    <div class="no-vs-msg__title">
        There is no sessions available at the moment
    </div>
    <div class="no-vs-msg__description">
        Please wait a little to meet new people around you!
        The new sessions will be displayed here automatically.
    </div>
</div>

<script>
    (() => {
        let isAuthorized = false;
        let isAdmin = false;
        let list = document.getElementById('vs-list');
        let skeleton = document.getElementById('vs-list-skeleton');
        let noVideoSessionsMessage = document.getElementById('no-vs-msg');

        document.addEventListener('authorized', e => {
            isAuthorized = true;
            isAdmin = e.detail
        })

        function itemClickHandler() {
            if (!isAuthorized) {
                let loginBtn = document.querySelector('.btn-login-tm');
                if (loginBtn !== null) loginBtn.click();
                return;
            }
            document.dispatchEvent(new CustomEvent('show-video-session-details', {
                detail: this.parentNode.__vs
            }));
        }

        // Update video sessions list.
        document.addEventListener('video-sessions-list-update', async e => {
            if (list !== null) {
                list.innerHTML = '';
                if (e.detail.items.length === 0) {
                    await fadeOut(skeleton);
                    await fadeIn(noVideoSessionsMessage);
                    return;
                } else {
                    await fadeOut(noVideoSessionsMessage)
                }
                for (let i = 0; i < e.detail.items.length; i++) {
                    let vs = e.detail.items[i];

                    let item = document.createElement('div');
                    item.className = 'vs-list-item';
                    item.dataset.id = vs.id;
                    item.__vs = vs;

                    let info = createSessionInfo(vs, isAdmin);
                    info.addEventListener('click', itemClickHandler);
                    item.appendChild(info);

                    list.appendChild(item);
                }
                await fadeOut(skeleton);
                await fadeIn(list);
            }
        });
    })();
</script>