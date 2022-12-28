<template>
    <div class="chat-search">
        <div class="chat-search_form">
                <input type="text" v-model.trim="search" placeholder="искать..." class="chat-search_form_input">
            <div class="clear" @click="clear"><p>+</p></div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Search",
        // props: [ 'searchClear '],
        data () {
            return {
                search : ''
            }
        },
        methods: {
            clearField () {
                this.search = ''
            },
            clear (){
                this.search = ''
                this.$emit('clear')
            }
        },
        watch: {
            search: function (newQuestion, oldQuestion) {
                if (newQuestion.length === 0) {
                    this.clear(); return
                }
                if (newQuestion.length >= 2) {
                    let self = this
                    let data = {search: newQuestion}
                    axios.post('/api/chat/search', data)
                        .then(function (response) {
                            self.$emit('getUsers' , response.data)
                        })
                        .catch(function (error) {
                            console.log(error);
                        })
                }
            }
        }
    }
</script>

<style lang="scss">
    .chat {
        &-search {
            padding: 10px;
            height: 60px;
            box-sizing: border-box;
            &_form {
                position: relative;
                height: 40px;
                &_input {
                    box-sizing: border-box;
                    outline: none;
                    border-radius: 10px;
                    width: 100%;
                    height: inherit;
                    border: 1px solid #b5b3b3;
                    padding: 5px 30px 5px 5px;
                    font-size: 16px;
                    color: #2196f3;
                    background: #b5b3b30d;
                    position: relative;
                    &:focus {
                        border: 1px solid #2196f3;
                        background: #fff;
                    }
                    &:active {
                        border: 1px solid #2196f3;
                        background: #fff;
                    }
                    &:-webkit-autofill,
                    &:-webkit-autofill:hover,
                    &:-webkit-autofill:active,
                    &:-webkit-autofill:focus {
                        -webkit-text-fill-color:#2196f3;
                        transition: background-color 5000s ease-in-out 0s;
                    }
                }

               & .clear {
                   position: absolute;
                   right:0;
                   top:50%;
                   content: "+";
                   transform: translateY(-50%) ;
                   width: 30px;
                   height: 100%;
                   cursor:pointer;
                   display: flex;
                   align-items: center;
                   justify-content: center;
                   & > p {
                       font-size: 26px;
                       transform: rotate(45deg);
                   }
                }
            }
        }
    }
</style>