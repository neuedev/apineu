<template>
  <edit-page v-bind="$attrs">
    <template #fields>
      <form-field-text
        name="name"
        label="Name"
      />
    </template>
  </edit-page>
</template>


<script>
import { Author } from '@/models'
import { Component, Vue } from 'vue-property-decorator'

@Component
export default class AuthorEdit extends Vue {
  static getEditRouteConfig () {
    return {
      ModelClass: Author,

      getAction: Author.getAction('get_author'),

      updateAction: Author.getAction('save_author'),

      fields: {
        name: true
      }
    }
  }
}
</script>
