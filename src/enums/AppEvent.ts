enum AppEvent
{
    AUTH_REQUIRED = "auth-required",
    CONNECTED = "connected",
    AUTHORIZED = "authorized",
    DISCONNECTED = "disconnected",
    UPDATE_USER = "update-user",
    ACTIVE_VIDEO_SESSION = "active-video-session",
    VIDEO_SESSION_DETAILS = "video-session-details",
    VIDEO_STREAM_READY = "video-stream-ready",
    LOCAL_VIDEO_STREAM_READY = "local-video-stream-ready",
    REMOTE_VIDEO_STREAM_READY = "remote-video-stream-ready",
    VIDEO_SESSIONS_LIST = "video-sessions-list",
    COUNTRIES_LIST = "countries-list",
    HIGHLIGHT_MEMBERS = "highlight-members",
    ERROR_MESSAGE = "error-message",
    START_TALK = "start-talk",
    END_TALK = "end-talk",
    TALK_RESULT = "talk-result",
    NOTIFICATIONS_LIST = "notifications-list",
    NEW_NOTIFICATION = "new-notifications",
    SEE_NOTIFICATION = "see-notification",
    CONVERSATIONS_LIST = "conversations-list",
    INCOMING_MESSAGE = "incoming-message",
    MESSAGES_LIST = "messages-list",
    SEE_MESSAGE = "see-message",
    CONVERSATION_UPDATE = "conversation-update",
}

export default AppEvent