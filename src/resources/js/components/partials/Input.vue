<template>
    <div class="chat-input">
        <div class="chat-input_form">
            <div class="chat-input_form_reply" v-if="reply">{{reply.message}} <i class="icon-delete" @click="deleteReply"></i></div>
            <textarea ref="text" type="text" v-model.trim="message" @keydown="typing" placeholder="пишите здесь..." class="chat-input_form_input"  autofocus  autocomplete="off"></textarea>
           <div class="icon-send" @click.stop="sendMessage" v-if="message"></div>
        </div>
        <div class="chat-input_alert" v-if="alert" v-click-outside="closeAlert">
            <p class="text">Вначале разблокируйте</p>
            <p class="buttom" @click="unignore">разблокируйте</p>
            <p class="cancell" @click="closeAlert">отмена</p>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Input",
        props: [ 'user', 'me', 'chatId', 'ignored'],
        data(){
            return {
                message: null,
                iconSend: 'scriptologia/chat/img/send.svg',
                isTyping: false,
                typingTimer: false,
                alert: false,
                reply: false
            }
        },
        methods: {
            deleteReply () {
                this.reply = false
            },
            setReply ( data) {
                this.reply = data
            },
            unignore() {
                this.$emit('unignore')
                this.closeAlert()
            },
            closeAlert() {
                this.alert = false;
            },
            typing () {
                if (this.channel ) this.channel.whisper('typing' , this.me) ;

                let oField = this.$refs.text
                oField.style.height = 0
                let scrollH = oField.scrollHeight > oField.clientHeight ? oField.scrollHeight-10 : oField.clientHeight -10;
                oField.style.height = scrollH+'px';console.log(scrollH)
            },
            sendMessage () {
                if(this.ignored) {
                    this.alert = true;
                }
                else {
                    let self = this
                    let data = {message: this.message, me: this.me, user: this.user, reply: this.reply? this.reply : false}
                    axios.post('/api/chat/send-message', data)
                        .then(function (response) {
                            self.message = ''
                            self.$refs.text.removeAttribute('style')
                            self.reply = false
                        })
                        .catch(function (error) {
                            console.log(error);
                        })
                }
            }
        },
        computed: {
            channel(){
                if(this.user && !this.ignored) { return window.Echo.join('chat.to-user-'+ this.user.id) }
            }
    },
        mounted () {
            this.message = null
            if (this.me) window.Echo.join('chat.to-user-' + this.me.id)
                .listenForWhisper('typing', (e) => {
                    this.isTyping = e
                    this.$emit('typing', e)
                    if (this.typingTimer) clearTimeout(this.typingTimer)
                    this.typingTimer = setTimeout(() => {
                        this.isTyping = false
                        this.$emit('typing', this.isTyping)
                    }, 2000)
                });

            this.$root.$on('reply', data => {
                this.reply = data
            });
        }
    }
</script>

<style lang="scss">
    .chat {
        &-input {
            border-top: 1px solid #c1bfbf4a;
            position: relative;
            &_form {
                position: relative;
                &_reply {
                    background: #f8f4ec;
                    border-left: 2px solid #b17d36;
                    margin: 3px;
                    padding: 3px;
                    & .icon-delete {
                        width: 1rem;
                        height: 1rem;
                        display: block;
                        position: absolute;
                        right: 0;
                        top: 0;
                        -webkit-mask: url('../../../img/delete.svg');
                        -webkit-mask-size: contain;
                        mask-size: contain;
                        -webkit-mask-repeat: no-repeat;
                        mask-repeat: no-repeat;
                        -webkit-mask-position: center;
                        mask-position: center;
                        background-color: red;
                        cursor: pointer;
                        &:hover {
                            transform: rotateZ(90deg);
                        }
                    }
                    }
                &_input {
                    outline: none;
                    width: 100%;
                    min-height: 29px;
                    height: 29px;
                    max-height: 100px;
                    border:none;
                    padding: 5px 30px 5px 5px;
                    font-size: 16px;
                    color: #767778;
                    background: #fff;
                    position: relative;
                    box-sizing: border-box;
                    overflow: hidden;
                }
                & .icon-send {
                    right: 0;
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    cursor: pointer;
                    width: 30px;
                    height: 30px;
                    -webkit-mask: url('../../../img/send.svg');
                    mask: url('../../../img/send.svg');
                    -webkit-mask-size: contain;
                    mask-size: contain;
                    -webkit-mask-repeat: no-repeat;
                    mask-repeat: no-repeat;
                    background-color: #2196f3;
                    -webkit-mask-position: center;
                    mask-position: center;
                }
            }

            &_alert {
                background: #fff;
                position: absolute;
                top: 0;
                transform: translateY(-100%);
                left: 0;
                right: 0;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                padding: 1rem;
                & > .text {
                    margin: 0 1em;
                    line-height: 150%;
                    flex: 1 0 auto;
                    font-size: 1.25rem;
                }
                & > .buttom {
                    color: #58d30c;
                    margin: 0 1em;
                    cursor: pointer;
                    line-height: 150%;
                }
                & > .cancell {
                    color: #2196f3;
                    margin: 0 1em;
                    cursor: pointer;
                    line-height: 150%;
                }
            }
        }
    }
</style>