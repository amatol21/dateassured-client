import Action from "./enums/Action"
import VideoSessionStatus from "./enums/VideoSessionStatus";
import MessageType from "./enums/MessageType";

export interface IdContainer
{
    id:number
}

export interface VideoSessionMember
{
    id: number
    name: string
    photoUrl: string
}

export interface VideoSessionBaseInfo
{
    sexuality: string
    purpose: string
    startedAt: number
    talkDuration: number
    delayBetweenTalks: number
    teamSize: number
    country: string
    region: string
    city: string
}

export interface VideoSessionInfo extends VideoSessionBaseInfo
{
    id: number
    title: string
    members: VideoSessionMember[]
    timeToNextTalk?: number
    timeToTalkEnd?: number
    status: VideoSessionStatus
}

export interface VideoSessionsListInfo
{
    totalCount: number
    page: number
    pageSize: number
    items: VideoSessionInfo[]
}

export interface User
{
    id: number
    username: string
    age: number
    gender: number
    photoUrl: string
}

export interface CountryInfo
{
    code: string
    name: string
    count: number
}

export interface SimpleVideoSessionInfo
{
    id: number
    country: string
    startedAt: number
}

export interface Notification
{
    id: number
    type: number
    payload: SimpleVideoSessionInfo

}

export interface ChatMember
{
    id: number
    username: string
    age: number
    gender: number
    photoUrl: string
}

export interface Conversation
{
    id: number
    members: ChatMember[]
    lastMessage: string|null
}

export interface Message
{
    id: number
    sender: ChatMember
    text: string
    type: number
    time: number
    status: number
    conversationId?: number
}

export interface TalkResult
{
    videoSessionId: number
    user: User
    rateFromMe: number
    rateFromAnother: number
}


export interface SignalingMessage
{
    action: Action
    id?: number
    sdp?: any
    ice?: RTCIceCandidate
    messageId?: number
    message?: string|Message
    messages?: Message[]
    fromId?: number
    userId?: number
    isAdmin?: boolean
    payload?: any
    videoSessionId?: number
    members?: number[]
    videoSessionInfo?: VideoSessionBaseInfo|IdContainer
    videoSessions?: VideoSessionInfo[]
    country?: string
    countries?: CountryInfo[]
    initiator?: number
    rate?: number
    comment?: string
    videoId?: string
    index?: number
    file?: string
    line?: number
    logs?: boolean
    turnServers?: string[]
    notifications?: Notification[]
    notification?: Notification
    conversations?: Conversation[]
    conversation?: Conversation
    conversationId?: number
    text?: string
    type?: MessageType|number
    talkResult?: TalkResult
}

export interface HighlightInfo
{
    videoSessionId: number
    members: number[]
}