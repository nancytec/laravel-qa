<template>
    <div class="media post">

        <vote :model="answer" name="answer"></vote>

        <div class="media-body">
            <form v-if = "editing == true" @submit.prevent="update">
                <div class="form-group">
                    <textarea rows="10" v-model="body" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-outline-secondary" :disabled="isInvalid">Update</button>
                <button @click="cancel" type="button" class="btn btn-outline-secondary">Cancel</button>
            </form>
            <div v-else>
                <div v-html="bodyHtml"></div>
                <div class="row">
                    <div class="col-4">
                        <div class="ml-auto">

                            <a v-if="authorize('modify', answer)" @click.prevent="edit" class="btn btn-sm btn-outline-info">Edit</a>

                            <button v-if="authorize('modify', answer)" @click="destroy" class="btn btn-sm btn-outline-danger">Delete</button>

                        </div>
                    </div>
                    <div class="col-4"></div>
                    <div class="col-4">
                        <user-info :model="answer" label="Answered" ></user-info>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    import Vote from "./Vote";
    import UserInfo from "./UserInfo";
    export default {
        props: ['answer'],

        components: {
            Vote,
            UserInfo
        },

        data() {
            return {
                editing: false,
                body: this.answer.body,
                bodyHtml: this.answer.body_html,
                id: this.answer.id,
                questionId: this.answer.question_id,
                beforeEditCache: null,
            }

        },

        methods: {
            edit () {
                this.beforeEditCache = this.body;
                this.editing = true;
            },

            cancel (){
              this.body = this.beforeEditCache;
              this.editing = false;
            },

            update () {
                axios.patch(this.endpoint, {
                    body: this.body
                })
                    .then(res => {
                        console.log(res);
                        this.editing = false;
                        this.bodyHtml = res.data.body_html;
                        this.$toast.success(res.data.message, "Success", {timeout: 3000});
                    }) // If ajax call is successful
                    .catch( err => {
                        this.$toast.error(err.response.data.message, "Error", {timeout: 3000});
                    }); // If ajax call is fails
            },

            destroy () {

                this.$toast.question('Are you sure about that?', "Confirm",{
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    id: 'question',
                    zindex: 999,
                    title: 'Hey',
                    message: 'Are you sure about that?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>',  (instance, toast) => {

                                axios.delete(this.endpoint)
                                    .then(res => {
                                        $(this.$el).fadeOut(500, () => {
                                            this.$toast.success(res.data.message, "Success", {timeout: 3000});
                                        })
                                    });

                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');

                        }, true],
                        ['<button>NO</button>', function (instance, toast) {

                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');

                        }],
                    ]
                });

            }
        },

        computed: { //This is equivalent to updated function in Livewire but this allows many methods to be defined
            isInvalid () {
                return this.body.length < 10; //this method will return true if body is less than 10
            },

            endpoint () {
                return `/questions/${this.questionId}/answers/${this.id}`;
            }
        }
    }
</script>

