<template>
  <div>
    <h1>Allocations</h1>
    <b-table
        id="allocation-table"
        striped
        hover
        sticky-header="true"
        no-border-collapse="true"
        :per-page="perPage"
        :items="items"
        :fields="fields"
        :current-page="currentPage"
    >
    </b-table>

    <b-row>
      <b-col>
        <label class="mr-sm-2" for="inline-form-custom-select-pref">Items per Page</label>
        <b-form-select id="inline-form-custom-select-pref" v-model="perPage" :options="pageOptions"></b-form-select>
      </b-col>
      <b-col>
        <b-pagination
            v-model="currentPage"
            v-if="perPage !== 0"
            aria-controls="allocation-table"
            :total-rows="rows"
            :per-page="perPage"
            first-text="First"
            last-text="Last"
            align="right"
        ></b-pagination>
      </b-col>
    </b-row>
  </div>
</template>

<script>
export default {
  name: "Allocations",
  data() {
    return {
      perPage: 5,
      currentPage: 1,
      pageOptions: [5, 10, 25, 50, { value: 0, text: "show all" }],
      fields: ['first_name', 'last_name', 'age'],
      items: [
        { age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
        { age: 21, first_name: 'Larsen', last_name: 'Shaw' },
        { age: 89, first_name: 'Geneva', last_name: 'Wilson' },
        { age: 38, first_name: 'Jami', last_name: 'Carney' },
        { age: 44, first_name: 'Jameson', last_name: 'Clack' },
        { age: 26, first_name: 'Anders', last_name: 'Soother' },
        { age: 83, first_name: 'Moore', last_name: 'McGray' },
        { age: 32, first_name: 'Donald', last_name: 'Pool' }
      ]
    }
  },
  computed: {
    rows() {
      return this.items.length;
    },
  },
}
</script>

<style scoped>

</style>
