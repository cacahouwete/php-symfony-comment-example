<template>
  <v-app>
    <v-container>
      <v-card>
        <v-card-title>
          Espace commentaire
        </v-card-title>
        <v-card-text>
          <v-form ref="comment_form" @submit.prevent="handleSubmit">
            <div v-if="itemToRespond === null && isAuthenticated">
              <v-textarea v-model="dto.content" />
              <v-btn icon="mdi-send" type="submit" />
            </div>
            <div v-if="!isAuthenticated">
              <p>
                Pour poster un commentaire, veuillez vous connecter avec l'un des provider suivant:
              </p>
              <v-btn icon="mdi-google" type="button" href="/connect/google" />
            </div>
            <comment-items
                :comments="comments"
                :dto="dto"
                :item-to-respond="itemToRespond"
                :is-authenticated="isAuthenticated"
                @content="dto.content = $event.target.value"
                @respond="itemToRespond = $event"
                @rate="handleRate"
            />
            <v-btn block v-if="restToLoad > 0" @click="loadNextPage">Load more ({{ restToLoad }})</v-btn>
          </v-form>
        </v-card-text>
      </v-card>
    </v-container>
  </v-app>
</template>

<script>
import { mapActions, mapGetters, mapState } from "vuex";
import CommentCreate from "../payload/CommentCreate";
import CommentItems from "./CommentItems.vue";
import CommentUpdate from "../payload/CommentUpdate";
import CommentFilter from "../model/filters/CommentFilter";
import { th } from "vuetify/locale";

export default {
  name: "Comment",
  components: { CommentItems },
  data() {
    return {
      groupKey: null,
      isAuthenticated: false,
      dto: new CommentCreate(),
      itemToRespond: null,
    }
  },
  computed: {
    ...mapState({
      comments: (state) => state.comment.items
    }),
    ...mapGetters({
      restToLoad: 'comment/restToLoad'
    })
  },
  mounted() {
    this.groupKey = this.$el.parentElement.getAttribute('data-group-key')
    this.isAuthenticated = this.$el.parentElement.getAttribute('data-authenticated') === '1'
    const filter = new CommentFilter()
    filter.groupKey = this.groupKey
    filter["order[createdAt]"] = 'desc'
    filter["exists[parent]"] = 'false'
    this.loadByGroupKey(filter)
  },
  methods: {
    ...mapActions({
      loadByGroupKey: 'comment/loadByGroupKey',
      loadNextPage: 'comment/loadNextPage',
      createComment: 'comment/create',
      createPatch: 'comment/patch',
    }),
    handleSubmit() {
      if (!this.$refs.comment_form.validate()) {
        return
      }

      if (this.itemToRespond !== null) {
        this.dto.parentId = this.itemToRespond.id
      }
      this.dto.groupKey = this.groupKey
      this.createComment(this.dto).then(() => {
        this.dto = new CommentCreate()
        this.itemToRespond = null
      })
    },
    handleRate(event) {
      console.log(event)
      const payload = new CommentUpdate();
      payload.rate = parseFloat(event.event.target.value)

      this.createPatch({payload, id: event.item.id})
    },
  }
}
</script>
