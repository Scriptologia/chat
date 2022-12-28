<template>
    <div class="chat-user">
        <ul class="chat-user-list" v-if="users.length">
              <li class="item" :class="{active: userActive && userActive.id === user.user.id}" v-for="(user, index) in users" :key="index" @click="selectedUser(user)">
                <div class="item-img">
                    <img :src="user.user.hasOwnProperty('img') ? user.user.img : defautImg" alt="">
                    <span v-if="user.active" class="item-img_active"></span>
                </div>
                <div class="item-text">
                    <p class="item-text_name">{{user.user.name}}</p>
                    <p class="item-text_info"><span class="" v-if="isTyping && isTyping.id === user.user.id">пишет...</span></p>
                </div>
                <div class="item-alarm">
                    <div class="count-new" v-if="user.countNew">{{user.countNew}}</div>
                </div>
            </li>
        </ul>
        <p v-else class="no-result">Ничего не найдено</p>
    </div>
</template>

<script>
    export default {
        name: "ContactsList",
        props: [
            'users', 'userActive', 'isTyping'
        ],
        data() {
            return {
                defautImg: 'scriptologia/chat/img/user.png',
                // active: true
            }
        },
        methods: {
            selectedUser(user) {
                this.$emit('selectedUser', user)
            }
        },
    }
</script>

<style lang="scss">
    @import '../../../sass/app.scss';

.chat {
        &-user {
            overflow-y: auto;
            height: 100%;
            padding-right: 5px;
            height: calc(100% - 60px);
            @include scroll (3px, 100%, blue, #767778, 2px, rgba(62, 208, 57, 0.231372549), black);
            & .no-result {
                text-align: center;
                padding: 10px;
            }
            &-list {
                list-style: none;
                margin: 0;
                padding: 0;
                padding-left: 5px;
                padding-right: 10px;
                &  .item {
                    cursor: pointer;
                    &.active {
                        background: #2196f3;
                        & * {
                            color: #fff;
                        }
                        &  .count-new {
                            background: #fff;
                            color: #000;
                        }
                    }
                    &:not(.active):hover {
                        background: #fbfbfb;
                    }
                    height: 30px;
                    display: flex;
                    padding: 3px 3px 3px 0;
                    box-sizing: content-box;
                    border-bottom: 1px solid #45454536;
                    &-img {
                        width: 30px;
                        height: inherit;
                        position: relative;
                        & > img {
                            width: inherit;
                            height: inherit;
                            border-radius: 50%;
                        }
                        &_active {
                            position: absolute;
                            top: -2px;
                            right: -2px;
                            width: 0.5rem;
                            height: 0.5rem;
                            background: #37ff00;
                            -webkit-border-radius: 50%;
                            -moz-border-radius: 50%;
                            border-radius: 50%;
                        }
                    }
                    &-text {
                        height: inherit;
                        padding-left: 10px;
                        padding-right: 10px;
                        flex: 1 0 calc(100% - 60px);
                        overflow: hidden;
                        white-space: nowrap;
                        &_name {
                            font-size: 16px;
                            font-weight: 600;
                            line-height: 100%;
                            padding: 0;
                            margin: 0;
                            text-overflow: ellipsis;
                            overflow: hidden;
                            color: #767778;
                        }
                        &_info {
                            font-size: 12px;
                            line-height: 100%;
                            padding: 0;
                            margin: 0;
                            color: #767778;
                            text-overflow: ellipsis;
                            overflow: hidden;
                        }
                    }
                    &-alarm {
                        width: 30px;
                        align-items: flex-start;
                        display: flex;
                        justify-content: flex-end;
                        & > .count-new {
                            width: fit-content;
                            max-width: 100%;
                            background: #2196f3;
                            border-radius: 20px;
                            color: #fff;
                            font-size: 10px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            padding: 1px 4px;
                        }
                    }
                }
            }
        }
    }
</style>