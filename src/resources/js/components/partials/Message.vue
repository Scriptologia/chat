<template>
    <div class="chat">
    <div class="chat-message" ref="feed" :key="user.id">
        <template v-if="messages && messages.length && typeof(messages) != 'undefided' ">
            <div class="chat-message_item" v-for="(message, index) in messages" :key="index"
                 :class="[message.from === user.id ? 'from' : 'to']"
                 @click.stop="showOptions(message)"
                 v-if="!message.trashed"
            >
                <span class="date">{{formatDate(message.date)}}</span>
                 <p class="message">{{message.message}} </p>
                <div class="readed"><div class="icon" :class="{readed: message.readed}" v-if="message.from !== user.id"></div></div>
            </div>
            <div v-else class="chat-message_item trashed" :class="[message.from === user.id ? 'from' : 'to']">сообщение удалено</div>
        </template>
        <h5 v-else class="no-messages">Начните диалог</h5>
    </div>
        <div class="options" v-if="currentOptions" v-click-outside="closeModal">
            <ul class="options_list">
                <li class="options_item" @click="trash"><i class="options_icon icon icon-trash"></i>
                    <p class="options_item_method">удалить</p>
                </li>
                <li class="options_item" @click="reply"><i class="options_icon icon icon-reply"></i>
                    <p class="options_item_method">ответить</p>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Message",
        props: ['messages', 'user', 'me', 'chatId'],
        data() {
            return {
                currentOptions: false,
            }
        },
        methods: {
            closeModal (){
                console.log('fff', this.currentOptions)
                this.currentOptions = false
            },
            trash () {
                let data  =  { chat_id: this.chatId, message: this.currentOptions}
                this.currentOptions = false
                axios.post('/api/chat/trash', data)
                    .then(function (response) {
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
            reply () {},
            showOptions (message = false) {
                this.currentOptions = message;
            },
            scrollToBottom (){
                let item = this.$refs.feed
                setTimeout( () => {
                    item.scrollTop = item.scrollHeight - item.clientHeight;
                } , 100);
            },
            formatDate(date0) {
                let date = new Date(date0);
                let diff = new Date() - date; // разница в миллисекундах

                if (diff < 1000) { // меньше 1 секунды
                    return 'прямо сейчас';
                }

                let sec = Math.floor(diff / 1000); // преобразовать разницу в секунды

                if (sec < 60) {
                    return sec + ' сек. назад';
                }

                let min = Math.floor(diff / 60000); // преобразовать разницу в минуты
                if (min < 60) {
                    return min + ' мин. назад';
                }

                // отформатировать дату
                // добавить ведущие нули к единственной цифре дню/месяцу/часам/минутам
                let d = date;
                d = [
                    '0' + d.getDate(),
                    '0' + (d.getMonth() + 1),
                    '' + d.getFullYear(),
                    '0' + d.getHours(),
                    '0' + d.getMinutes()
                ].map(component => component.slice(-2)); // взять последние 2 цифры из каждой компоненты

                // соединить компоненты в дату
                return d.slice(0, 3).join('.') + ' ' + d.slice(3).join(':');
            }
        },
        mounted (){
            this.scrollToBottom()
        },
        watch : {
            messages () {
                this.scrollToBottom()
            }
        }
    }
</script>

<style lang="scss">
    @import '../../../sass/app.scss';
    .chat {
        position: relative;
        width: 100%;
        height: 100%;
        &-message {
            background: rgb(190 222 170);
            width: 100%;
            height: 100%;
            overflow-y: auto;
            @include scroll (3px, 100%, blue, #767778, 2px, rgba(62, 208, 57, 0.231372549), black);
            &_item {
                outline: none;
                max-width: 70%;
                width: fit-content;
                padding:5px;
                margin:5px;
                position: relative;
                &.from {
                    box-shadow: 1px 1px 1px 1px #adc993;;
                    background: #ffffff;
                    margin-left: 10px;
                    &:before {
                        left:0;
                        border-left: 10px solid transparent;
                        border-bottom: 10px solid #fff;
                        left: -10px;
                    }
                }
                &.to {
                    box-shadow: 1px 1px 4px 1px #acc4a1;
                    margin-right: 10px;
                    background: #dffac7;
                    margin-left: auto;
                    &:before {
                        right:0;
                        border-right: 10px solid transparent;
                        border-bottom: 10px solid #dffac7;
                        right: -10px;
                    }
                }
                &.to:before, &.from:before {
                    content:'';
                    position: absolute;
                    bottom:0;
                }
                & .message {
                    font-size: 16px;
                    line-height: 90%;
                    white-space: pre-wrap;
                    word-wrap: break-word;
                    margin: 0;
                }
                & .date {
                    font-style: italic;
                    font-size: 11px;
                }
                & .readed {
                    width: 15px;
                    height: 15px;
                    margin-left: auto;
                    & > .icon {
                        width: inherit;
                        height: inherit;
                        cursor: pointer;
                        -webkit-mask: url('../../../img/double-check.svg');
                        mask: url('../../../img/send.svg');
                        -webkit-mask-size: contain;
                        mask-size: contain;
                        -webkit-mask-repeat: no-repeat;
                        mask-repeat: no-repeat;
                        background-color: #2196f3;
                        -webkit-mask-position: center;
                        mask-position: center;
                        background: #000;
                        &.readed {
                            background: #2196f3;
                        }
                    }
                }
                &.trashed {
                    background: #dfe0e0;
                    &:before { content: none;}
                }
            }
            & > .no-messages {
                text-align: center;
                font-size: 24px;
                color: #fff;
            }
        }
    }
    .options {
        width: min-content;
        position: absolute;
        top: 50%;
        left: 50%;
        background: #ffffff;
        transform: translate(-50%, -50%);
        &_item {
            color: #2196f3;
            padding: 0 .5rem;
            &:not(:last-child) {margin-bottom: 3px;}
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            height:30px;
            width: 100%;
            box-sizing: border-box;
            &:hover {
                background: #2196f321;
            }
            & > .options_icon {
                margin-right: .5rem;
            }
            &_method {
                display: inline;
            }
        }
        &_list {
            list-style: none;
            margin: .5rem 0;
            padding: 0;
           & .icon {
                width: 15px;
                height: 15px;
               display: block;
                -webkit-mask-size: contain;
                mask-size: contain;
                -webkit-mask-repeat: no-repeat;
                mask-repeat: no-repeat;
                background-color: #2196f3;
                -webkit-mask-position: center;
                mask-position: center;

                &-reply {
                    -webkit-mask: url('../../../img/reply.svg');
                }
                &-trash {
                    -webkit-mask: url('../../../img/trash.svg');
                }
            }
        }
    }
</style>