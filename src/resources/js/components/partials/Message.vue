<template>
    <div class="chat">
        <div class="chat-message" ref="feed" :key="user.id" @scroll="scrollButtonShow">
             <template v-if="messages && messages.length && typeof(messages) !== 'undefided' ">
            <div class="wraper-message" v-for="(message, index) in messages" :id="message.date">
               <div class="chat-message_item"
                 :key="index"
                 :class="[message.from === user.id ? 'from' : 'to']"
                 @click.stop="showOptions(message)"
                 v-if="!message.trashed && !(ignored && message.status === 'sended')"
            >
                <span class="date">{{formatDate(message.date)}}</span>
                 <div class="reply" v-if="message.reply">
                     <a :href="'#'+message.reply.date">{{message.reply.message}}</a>
                 </div>
                 <p class="message">{{message.message}} </p>
                <div class="readed" v-if="message.from !== user.id">
                    <i class="icon icon-sended" v-if="message.status === 'sended' "></i>
                    <i class="icon icon-readed" :class="{readed: message.status === 'readed'}" v-else></i>
                </div>
            </div>
               <div  v-if="message.trashed" class="chat-message_item trashed" :class="[message.from === user.id ? 'from' : 'to']">сообщение удалено</div>
           </div>
        </template>
             <h5 v-else class="no-messages">Начните диалог</h5>
        </div>
        <div class="options_wrapper" v-if="currentOptions">
            <div class="options" v-click-outside="closeModal">
            <ul class="options_list">
                <li class="options_item" @click="trash" v-if="me.id === currentOptions.from"><i class="options_icon icon icon-trash"></i>
                    <p class="options_item_method">удалить</p>
                </li>
                <li class="options_item" @click="reply"><i class="options_icon icon icon-reply"></i>
                    <p class="options_item_method">ответить</p>
                </li>
            </ul>
        </div>
        </div>
        <div class="scroll-top" @click="scrollToBottom" v-if="showScroll"><i class="icon icon-bottom"></i></div>
    </div>
</template>

<script>
    export default {
        name: "Message",
        props: ['messages', 'user', 'me', 'chatId', 'ignored'],
        data() {
            return {
                currentOptions: false,
                showScroll: false,
                oldLength:0
            }
        },
        methods: {
            closeModal (){
                this.currentOptions = false
                document.querySelector(".wraper-message_selected").classList.remove("wraper-message_selected")
            },
            trash () {
                let data  =  { chat_id: this.chatId, message: this.currentOptions}
                this.currentOptions = false
                document.querySelector(".wraper-message_selected").classList.remove("wraper-message_selected")
                axios.post('/api/chat/trash-message', data)
                    .then(function (response) {
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
            reply () {
                this.$root.$emit('reply', this.currentOptions)
                this.closeModal()
            },
            showOptions (message = false) {
                this.currentOptions = message;
                event.target.closest(".wraper-message").classList.add("wraper-message_selected")
            },
            scrollToBottom (){
                let item = this.$refs.feed
                setTimeout( () => {
                    // item.scrollTop = item.scrollHeight - item.clientHeight;
                item.scrollBy({
                        top: item.scrollHeight - item.clientHeight,
                        behavior: 'smooth'
                    });
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
            },
            scrollButtonShow() {
                if (this.$refs.feed) {
                    if((this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight - 100) > this.$refs.feed.scrollTop) {
                        this.showScroll = true;
                    }
                    else {
                        this.showScroll = false;
                    }
                }
            }
        },
        mounted (){
            this.scrollToBottom()

            const smoothLinks = document.querySelectorAll('a[href^="#"]');
            for (let smoothLink of smoothLinks) {
                smoothLink.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation()
                    let id = smoothLink.getAttribute('href');

                    document.getElementById(id.replace('#', '')).scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            };
            },
        watch : {
            messages(newVal) {
                if(this.oldLength !== newVal.length) this.scrollToBottom();
                this.oldLength = newVal.length
            },
        }
    }
</script>

<style lang="scss">
    @import '../../../sass/app.scss';
    .chat {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: hidden;
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
                    & .icon {
                        width: inherit;
                        height: inherit;
                        cursor: pointer;
                        display: block;
                        background-color: grey;
                        &.icon-readed {
                            -webkit-mask: url('../../../img/double-check.svg');
                            mask: url('../../../img/double-check.svg');
                            -webkit-mask-size: contain;
                            mask-size: contain;
                            -webkit-mask-repeat: no-repeat;
                            mask-repeat: no-repeat;
                            -webkit-mask-position: center;
                            mask-position: center;
                        }
                        &.icon-sended {
                            -webkit-mask: url('../../../img/check.svg');
                            mask: url('../../../img/double-check.svg');
                            -webkit-mask-size: contain;
                            mask-size: contain;
                            -webkit-mask-repeat: no-repeat;
                            mask-repeat: no-repeat;
                            -webkit-mask-position: center;
                            mask-position: center;
                        }
                        &.readed {
                            background: #2196f3;
                        }
                    }
                }
                &.trashed {
                    background: #dfe0e0;
                    &:before { content: none;}
                }
                & .reply {
                    background: #f8f4ec;
                    border-left: 2px solid #b17d36;
                    margin: 0.5rem 0.1rem;
                    padding: 3px;
                }
            }
            & > .no-messages {
                text-align: center;
                font-size: 24px;
                color: #fff;
            }
        }
        & > .scroll-top {
            position: absolute;
            bottom: 1rem;
            right:1rem;
            width:3rem;
            height:3rem;
            background-color: #fff;
            border-radius: 50%;
            justify-content: center;
            align-items: center;
            display: flex;
            & > .icon {
                &-bottom {
                    width: inherit;
                    height: inherit;
                    margin-top: 0.3rem;
                    display: block;
                    -webkit-mask: url('../../../img/arrow-bottom.svg');
                    -webkit-mask-size: contain;
                    mask-size: contain;
                    -webkit-mask-repeat: no-repeat;
                    mask-repeat: no-repeat;
                    background-color: #2196f3;
                    -webkit-mask-position: center;
                    mask-position: center;
                }
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

        &_wrapper {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
        }
    }
    .wraper-message {
        &_selected {
            background:#f9f9f963;
        }
    }
</style>