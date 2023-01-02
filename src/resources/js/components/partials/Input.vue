<template>
    <div class="chat-input">
        <div class="chat-input_form">
            <input type="text" v-model.trim="message" @keydown="typing" placeholder="пишите здесь..." class="chat-input_form_input"  autofocus  autocomplete="off">
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
                alert: false
            }
        },
        methods: {
            unignore() {
                this.$emit('unignore')
                this.closeAlert()
            },
            closeAlert(){
                this.alert = false;
            },
            typing (){
                this.channel
                        .whisper('typing' , this.me)
            },
            sendMessage () {
                if(this.ignored) {
                    this.alert = true;
                }
                else {
                    let self = this
                    let data = {message: this.message, me: this.me, user: this.user, }
                    axios.post('/api/chat/send-message', data)
                        .then(function (response) {
                            // TODO очищать поле при положительном ответе с сервера
                            self.message = ''
                        })
                        .catch(function (error) {
                            console.log(error);
                        })
                }
            }
        },
        computed: {
            channel(){
                if(this.user) { return window.Echo.join('chat.to-user-'+ this.user.id) }
            }
    },
        mounted () {
            this.message = null
            if(this.me) window.Echo.join('chat.to-user-'+ this.me.id)
                .listenForWhisper('typing', (e) => {
                    this.isTyping = e
                    this.$emit('typing', e)
                    if (this.typingTimer) clearTimeout(this.typingTimer)
                    this.typingTimer = setTimeout(() => {
                        this.isTyping = false
                        this.$emit('typing', this.isTyping)
                    }, 2000)
                }) ;
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
                &_input {
                    outline: none;
                    width: 100%;
                    height: 100%;
                    border:none;
                    padding: 5px 30px 5px 5px;
                    font-size: 16px;
                    color: #767778;
                    background: #fff;
                    position: relative;
                    box-sizing: border-box;
                }
                & .icon-send {
                    right: 0;
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    cursor: pointer;
                    width: 30px;
                    height: 100%;
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