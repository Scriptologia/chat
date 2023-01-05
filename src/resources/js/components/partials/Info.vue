<template>
    <div class="chat-info">
        <div class="to-contact-list" @click="$emit('toUserList')">&#8592;</div>
        <template v-if="user">
        <div class="chat-info_img">
            <img :src="user.hasOwnProperty('img') ? user.img : defautImg" alt="">
        </div>
        <div class="chat-info_text">
            <p class="chat-info_text_name">{{user.name}} <span v-if="ignored ">заблокирован</span></p>
            <p class="chat-info_text_info"><span v-if="isTyping && isTyping.id === user.id" class="">пишет...</span></p>
        </div>
            <div class="menu-user">
                <i class="menu-user_icon icon-menu icon" @click.stop="menu = !menu"></i>
                <transition name="from-up">
                     <div class="menu-user_block" v-if="menu"  v-click-outside="closeMenu">
                    <ul class="menu-user_list">
                        <li class="menu-user_item" @click="deleteChat"><i class="menu-user_item_icon icon-delete icon"></i><p class="menu-user_item_text">удалить канал</p></li>
                        <li class="menu-user_item" @click="ignoreUser" v-if="!ignored"><i class="menu-user_item_icon icon-ignore icon"></i><p class="menu-user_item_text">игнорировать</p></li>
                        <li class="menu-user_item" @click="unIgnore" v-else><i class="menu-user_item_icon icon-ignore icon"></i><p class="menu-user_item_text">разблокировать</p></li>
                        <li class="menu-user_item"><i class="menu-user_item_icon icon-delete icon"></i><p class="menu-user_item_text">удалить канал</p></li>
                    </ul>
                </div>
                </transition>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        name: "Info",
        props: [
            'user', 'isTyping', 'chatId', 'ignored'
        ],
        data() {
            return {
                menu: false,
                defautImg: 'scriptologia/chat/img/user.png'
            }
        },
        methods: {
            deleteChat () {
                let self = this;
                axios.post('/api/chat/delete-chat', {user_id: this.user.id, chat_id: this.chatId})
                    .then(function (response) {
                        if(response.data.status) {
                            self.$emit('deleteChat', response.data.deletedChat)
                            self.menu = false
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
            ignoreUser () {
                let self = this;
                axios.post('/api/chat/ignore-user', {ignore_user_id: self.user.id})
                    .then(function (response) {
                        if(response.data.status && self.user.id === response.data.ignore_user_id ) {
                            self.$emit('ignored', {user_id: response.data.ignore_user_id, ignored: true})
                            self.menu = false
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
            unIgnore () {
                let self = this;
                axios.post('/api/chat/unignore-user', {unignore_user_id: self.user.id})
                    .then(function (response) {
                        if(response.data.status && self.user.id === response.data.unignore_user_id ) {
                            self.$emit('ignored', {user_id: response.data.unignore_user_id, ignored: false})
                            self.menu = false
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
            closeMenu() {
                this.menu = false
            },
        }
    }
</script>

<style lang="scss">
    .chat {
        &-info {
            border-bottom: 1px solid #7878784d;
            height: 60px;
            padding: 10px;
            box-sizing: content-box;
            display: flex;
            align-items: center;
            & > .to-contact-list {
                display:none;
                @media (max-width: 576px) {
                    display: block;
                    padding-right: 10px;
                    cursor:pointer;
                    &:hover {
                        color:  #2196f3;
                    }
                }
            }
             &_img {
                    width: auto;
                    height: inherit;
                    & > img {
                        width: inherit;
                        height: inherit;
                        border-radius: 50%;
                    }
                }
             &_text {
                    height: inherit;
                    padding-left: 10px;
                    padding-right: 10px;
                    /*flex: 1 0 calc(100% - 60px);*/
                    overflow: hidden;
                    white-space: nowrap;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-evenly;
                    &_name {
                        font-size: 20px;
                        font-weight: 600;
                        line-height: 100%;
                        padding: 0;
                        margin: 0;
                        text-overflow: ellipsis;
                        overflow: hidden;
                        color: #767778;
                        & > span {
                            font-size: .7em;
                            font-style: italic;
                            color: red;
                        }
                    }
                    &_info {
                        font-size: 16px;
                        line-height: 100%;
                        padding: 0;
                        margin: 0;
                        color: #767778;
                        text-overflow: ellipsis;
                        overflow: hidden;
                    }
                }
            & .menu-user {
                margin-left: auto;
                width: 25px;
                height: inherit;
                position: relative;
                &_icon {
                    width: 25px;
                    height: inherit;
                    background-color: #2196f3;
                }
                &_block {
                    width: fit-content;
                    position: absolute;
                    bottom: 0;
                    right: 0;
                    transform: translate(0, 100%);
                    background: #fff;
                    z-index: 100;
                    padding: 1rem 0;
                    border-radius: 5px;
                    box-shadow: 0px 8px 10px 0px #809573;
                    }
                &_list {
                    margin: 0;
                    padding: 0;
                }
                &_item {
                    align-items: center;
                    list-style: none;
                    display: flex;
                    white-space: nowrap;
                    padding:0 15px;
                    cursor: pointer;
                    &:hover {
                        background: rgba(33, 150, 243, 0.1294117647);;
                    }
                    &_icon {
                        margin-right: 5px;
                        height:20px;
                        width: 20px;
                    }
                    &_text {
                        font-size: 20px;
                        text-transform: capitalize;
                    }
                }
                }

            & .icon {
                display: block;
                cursor:pointer;
                background-color: #2196f3;
                &-delete {
                    -webkit-mask: url('../../../img/delete.svg');
                    -webkit-mask-size: contain;
                    mask-size: contain;
                    -webkit-mask-repeat: no-repeat;
                    mask-repeat: no-repeat;
                    -webkit-mask-position: center;
                    mask-position: center;
                    background-color: red;
                }
                &-ignore {
                    -webkit-mask: url('../../../img/ignore.svg');
                    -webkit-mask-size: contain;
                    mask-size: contain;
                    -webkit-mask-repeat: no-repeat;
                    mask-repeat: no-repeat;
                    -webkit-mask-position: center;
                    mask-position: center;
                    background-color: red;
                }
                &-menu {
                    -webkit-mask: url('../../../img/menu.svg');
                    -webkit-mask-size: contain;
                    mask-size: contain;
                    -webkit-mask-repeat: no-repeat;
                    mask-repeat: no-repeat;
                    -webkit-mask-position: center;
                    mask-position: center;
                }
            }
            }
    }
    /*annimation*/
.from-up {
    &-enter, &-leave-to {
        opacity: 0;
        transform: translate(0, 0) !important;
    }
    &-enter-to, &-leave {
        opacity: 1;
        transform: translate(0, 100%) !important;
    }
    &-enter-active, &-leave-active {
        transition: all .5s;
    }
}
</style>