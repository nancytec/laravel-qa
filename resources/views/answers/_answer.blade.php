<answer :answer="{{$answer}}" inline-template>

    <div class="media post">

        <vote :model="{{$answer}}" name="answer"></vote>

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
                            @can('update', $answer)
                                <a @click.prevent="edit" class="btn btn-sm btn-outline-info">Edit</a>
                            @endif
                            @can('delete', $answer)
                                <button @click="destroy" class="btn btn-sm btn-outline-danger">Delete</button>
                            @endif
                        </div>
                    </div>
                    <div class="col-4"></div>
                    <div class="col-4">
                        <user-info :model="{{$answer}}" label="Answered" ></user-info>
                    </div>
                </div>
            </div>

        </div>
    </div>


</answer>
