<template>
  <v-list :items="comments">
    <v-list-item v-for="item in comments">
      <template #title>
          <div class="font-weight-normal">
            <v-avatar :image="item.authorAvatar" /> <strong>{{ item.authorUsername }}</strong> @{{ item.createdAt.toLocaleString() }}
          </div>

          {{ item.content }}
          <div v-if="itemToRespond === item && isAuthenticated">
            <v-textarea :model-value="dto.content" @input="$emit('content', $event)" />
            <v-btn icon="mdi-close" type="button" @click="$emit('respond', null)"></v-btn>
            <v-btn icon="mdi-send" type="submit"></v-btn>
          </div>
      </template>
      <template #subtitle>
        <span class="text-grey-lighten-2 text-caption mr-2">
          ({{ Math.round(item.rate * 10) / 10 }})
        </span>
        <v-rating
            :model-value="item.rate"
            color="grey"
            active-color="yellow-accent-4"
            half-increments
            hover
            size="18"
            @input="$emit('rate', {item, event: $event})"
            :disabled="!isAuthenticated"
        ></v-rating>
        <v-btn v-if="itemToRespond !== item && isAuthenticated" @click="$emit('respond', item)">Répondre</v-btn>
      </template>
      <template #default>
        <comment-items
            :comments="item.children"
            :dto="dto"
            :item-to-respond="itemToRespond"
            :is-authenticated="isAuthenticated"
            @content="$emit('content', $event)"
            @respond="$emit('respond', $event)"
            @rate="$emit('rate', $event)"
        />
      </template>
    </v-list-item>
  </v-list>
</template>

<script>
import Comment from "../model/Comment";
import CommentCreate from "../payload/CommentCreate";

export default {
  name: "CommentItems",
  props: {
    comments: {
      type: Array,
      required: true,
    },
    itemToRespond: {
      type: Comment,
      required: false,
      default: () => null,
    },
    dto: {
      type: CommentCreate,
      required: false,
      default: () => new CommentCreate,
    },
    isAuthenticated: {
      type: Boolean,
      required: false,
      default: () => false,
    },
  },
}
</script>
