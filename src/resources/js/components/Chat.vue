<template>
    <div class="chat-container">
        <div class="chat-left">
            <Search @getUsers="insertUsers" @clear="clear" ref="search"></Search>
            <ContactsList :users="users" @selectedUser="toMessage" :userActive="user" :isTyping="isTyping"  ></ContactsList>
        </div>
        <div class="chat-right" :class="{ open: toMessageShow }">
            <Info :user="user" @toUserList="toMessageShow = false" :isTyping="isTyping" v-if="user" :key=" 'user' + user.id" :chatId="chatId" @deleteChat="deleteChat" :ignored="ignored" @ignored="ignoredStatus" ref="info"></Info>
            <Message v-if="user" :messages="messages" :me="me" :user="user" :key=" 'messages' + chatId" :chatId="chatId" :ignored="ignored"></Message>
            <Input v-if="user" :me="me" :user="user" @typing="getTyping" :chatId="chatId" :key=" 'input' + chatId" :ignored="ignored" @unignore="$refs.info.unIgnore()"></Input>
        </div>
    </div>
</template>

<script>
    import Input from './partials/Input.vue';
    import Info from './partials/Info.vue';
    import ContactsList from './partials/ContactsList.vue';
    import Message from './partials/Message.vue';
    import Search from './partials/Search.vue';

    export default {
        name: "Chat",
        components: { Input, Info,ContactsList, Message,Search } ,
        data(){
            return {
                users:[],
                myUsers:[],
                messages: [],
                user: null,
                chatId: false,
                me: null,
                toMessageShow : false,
                isTyping: false,
                searchClear: '',
                ignored : false
            }
        },
        methods: {
            ignoredStatus (obj) {
                this.ignored = obj.ignored
                let us =this.myUsers.find( it => {
                   return  it.user.id === obj.user_id
                })
                us.ignored = obj.ignored;
                if(!us.ignored) { this.readedSend() ; this.isJoin();}
                else { window.Echo.leave('chat.to-user-'+us.user.id );}
            },
            deleteChat (deletedChat ) {
                let index = this.myUsers.findIndex( it => {
                    return it.id === deletedChat.id;
                })
                if( index >= 0) {
                    this.myUsers.splice(index, 1)
                    this.clear()
                }
                this.toMessageShow = false
                this.messages = []
                this.user = null
            },
            isJoin(){
               if(!this.ignored) window.Echo.join('chat.to-user-'+ this.user.id )
            },
            getTyping (typing) {
                this.isTyping = typing
            },
            clear (){
                this.users = this.myUsers
            },
            insertUsers (users) {
                this.users = users.map(it => {
                    let messages = this.myUsers.find(item => {
                        if ( item.user.id === it.id ) { return item.messages }
                    })
                    return { id: false, countNew: 0, active: false, user: it, messages: messages ? messages: [], ignored: it.ignored}
                })
            },
            readedSend(){
                let self = this
                axios.post('/api/chat/read-message', {user_id: this.me.id, chat_id: this.chatId})
                    .then(function (response) {
                        // TODO
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
            toMessage (user){
                this.isTyping = false
                this.$refs.search.clearField()
                if( this.user ) window.Echo.leave('chat.to-user-'+this.user.id );
                this.messages = user.messages ? user.messages : [];
                this.chatId = user.id
                this.ignored = user.ignored
                if(!this.user || this.user.id !== user.user) {
                    this.user = user.user
                    this.toMessageShow = true
                }
                let newUser = this.myUsers.find( it => it.user.id === user.user.id)
                if ( !newUser ) this.myUsers.unshift(user) ;
                this.myUsers = this.myUsers.map( it => {
                    if(it.id === this.chatId)  it.countNew = 0;
                    return it;
                })

                this.clear()
                this.isJoin()
                if(user.id && !user.ignored) this.readedSend() ;
            },
            deliveredMessage (data) {
                axios.post('/api/chat/delivered-message', data)
                    .then(function (response) {
                        // TODO
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            }
        },
        mounted (){
            let self = this

            axios.get('/api/chat/get-me')
                .then(function (response) {
                    self.me = response.data
                })
                .then(function () {
                    window.Echo.join('chat.to-user-' + self.me.id)
                        .here((users) => {
                        let arr = users.map(it => it.id)
                        self.users = self.users.map(it => {
                            if (arr.includes(it.user.id)) it.active = true;
                            return it;
                        })
                    })
                        .joining((user) => {
                            self.users = self.users.map(it => {
                                if (it.user.id === user.id)  it.active = true;
                                return it;
                            })
                        })
                        .leaving((user) => {
                            self.users = self.users.map(it => {
                                if (it.user.id === user.id)  it.active = false;
                                return it;
                            })
                        })
                        .listen('.Scriptologia\\Chat\\Events\\SendMessageEvent', (data) => {
                            if( data.user.id !== self.me.id )  self.deliveredMessage(data);

                            let index ;
                            if(data.user.id === self.me.id) {
                                index = self.myUsers.findIndex( it => {
                                    return it.user.id === data.message.to;
                                })
                                self.chatId = data.chat_id
                            }
                            else {
                                index = self.myUsers.findIndex( it => {
                                    return it.user.id === data.message.from;
                                })
                            }

                                if( index >= 0) {
                                    let item = self.myUsers[index];
                                    item.messages.push(data.message)
                                    item.id = data.chat_id

                                    if( data.user.id !== self.me.id && ( !self.user || self.user.id !== data.user.id ) ) { item.countNew ? item.countNew++ : item.countNew = 1; }
                                    if (data.user.id !== self.me.id && self.user.id === data.user.id) self.readedSend();
                                }
                                else {
                                    let newObj = { user: data.user, messages : [data.message], id: data.chat_id, active: false, countNew: 1}
                                    self.myUsers.unshift( newObj )
                                }

                            self.clear()
                        })
                        .listen('.Scriptologia\\Chat\\Events\\TrashedMessageEvent', (data) => {
                            self.myUsers = self.myUsers.map( it => {
                                if(it.id === data.chat_id )  {
                                    it.messages = it.messages.map( item => {
                                        if( item.from === data.trashedMessage.from && item.date === data.trashedMessage.date) item = data.trashedMessage ;
                                        return item;
                                    });
                                    if(self.chatId === data.chat_id) {self.messages = it.messages ; self.chatId = data.chat_id ;}
                                }
                                return it;
                            })
                        })
                        .listen('.Scriptologia\\Chat\\Events\\DeletedMessageEvent', (data) => {
                            let index = self.myUsers.findIndex( it => {
                                return it.id === data.chatDeleted.id;
                            })
                            if( index >= 0) {
                                self.myUsers.splice(index, 1)
                                self.clear()
                            }
                            if(self.chatId === data.chatDeleted.id) {
                                self.toMessageShow = false
                                self.messages = []
                                self.user = null
                            }
                        })
                        .listen('.Scriptologia\\Chat\\Events\\ReadedMessageEvent', ({data}) => {
                            self.myUsers = self.myUsers.map( it => {
                                if(it.user.id === data.readed_user_id )  {
                                    it.messages = it.messages.map( item => {
                                        if( item.to === data.readed_user_id) item.status = 'readed';
                                        return item;
                                    });
                                    if(self.user && data.readed_user_id === self.user.id) {self.messages = it.messages ; self.chatId = data.chat_id ;}
                                }
                                return it;
                            })
                        })
                        .listen('.Scriptologia\\Chat\\Events\\DeliveredMessageEvent', ({data}) => {
                            self.myUsers = self.myUsers.map( it => {
                                if(it.id === data.chat_id )  {
                                    it.messages = it.messages.map( item => {
                                        if( item.from === data.from && data.date === item.date) item.status = 'delivered';
                                        return item;
                                    });
                                    if(self.user && data.from === self.user.id) {self.messages = it.messages ; self.chatId = data.chat_id ;}
                                }
                                return it;
                            })
                        })
                        .listen('.Scriptologia\\Chat\\Events\\IsIgnoredEvent', ({data}) => {
                            if (data.status) window.Echo.leave('chat.to-user-'+data.user_id );
                            if (!data.status) window.Echo.join('chat.to-user-'+data.user_id );
                        }) ;
                })
                .catch(function (error) {
                    console.log(error);
                })

            axios.get('/api/chat/contacts')
                .then(function (response) {
                    self.users = response.data.map( it => {
                        let sum = 0
                        for (let i = 0; i < it.length ; i++) {
                            if(it.from !== self.me.id && !id.status === 'readed') sum++;
                        }
                        it.countNew = sum
                        return it;
                    })
                    self.myUsers = self.users
                })
                .catch(function (error) {
                    console.log(error);
                })
        }

    }
</script>

<style lang="scss">
    .chat {
        &-container {
            height: 80vh;
            width:100%;
           display: flex;
            border: 1px solid #dedfdf;
            /*justify-content: center;*/
            background: #fff;
            position: relative;
            /*overflow: hidden;*/
            @media (max-width: 576px) {
                display: block;
                width: 100%;
            }
        }
        &-left {
            width: 250px;
            flex: 0 0 auto;
            border-right: 1px solid #8f90914a;
            overflow-y: hidden;
            @media (max-width: 576px) {
                width: 100%;
            }
        }
        &-right {
            background: #fff;
            flex:1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            @media (max-width: 576px) {
                transform: translateX(100%);
                &.open {
                    position: absolute;
                    top: 0;
                    left: 0;
                    bottom: 0;
                    z-index: 10;
                    transform: translateX(0%);
                    width: 100%;
                }
            }
        }
    }


</style>