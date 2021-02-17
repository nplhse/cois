<template>
  <div>
    <b-table
        id="allocation-table"
        striped
        hover
        :sticky-header="btableMaxHeight"
        no-border-collapse
        :busy="loading"
        :items="items"
        :fields="fields"
        :loading="loading"
    >
      <template #table-busy>
        <div class="text-center my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
      </template>

      <template #cell(actions)="row">
        <b-button size="sm" @click="row.toggleDetails">
          {{ row.detailsShowing ? 'Hide' : 'Show' }} Details
        </b-button>
      </template>

      <template #row-details="row">
        <b-card>
          <ul>
            <li v-for="(value, key) in row.item" :key="key">{{ key }}: {{ value }}</li>
          </ul>
        </b-card>
      </template>
    </b-table>

    <b-row>
      <b-col>
        <b-form-group>
          <label class="mr-sm-2" for="pagination-selector">Items per Page</label>
          <b-form-select id="pagination-selector" v-model="perPage" :options="pageOptions" size="sm"></b-form-select>
        </b-form-group>
      </b-col>
      <b-col>
        <b-pagination
            v-model="currentPage"
            aria-controls="allocation-table"
            :total-rows="totalItems"
            :per-page="perPage"
            first-text="First"
            last-text="Last"
            align="right"
        >
        </b-pagination>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import { fetchAllocations } from '../service/allocation-service';

export default {
  name: "AllocationList",
  data() {
    return {
      btableMaxHeight: '500px',
      totalItems: 0,
      perPage: 10,
      currentPage: 1,
      pageOptions: [10, 25, 50, 100],
      loading: true,
      fields: ['id', 'dispatchArea', 'supplyArea', 'hospital', 'createdAt', 'actions'],
      items: []
    }
  },
  watch: {
    currentPage() {
      this.loadAllocations();
    },
    perPage() {
      this.loadAllocations();
    }
  },
  created() {
    this.loadAllocations();
  },
  methods: {
    async loadAllocations() {
      this.loading = true;

      let response;
      try {
        response = await fetchAllocations(this.currentPage, this.perPage);

        this.loading = false;
      } catch (e) {
        this.loading = false;

        return;
      }

      this.items = response.data['hydra:member'];
      this.totalItems = response.data['hydra:totalItems'];
      // this.$refs.table.refresh();
    },
  },
  mounted () {
    var self = this
    // to update b-table max-height to have a freeze header (sticky-header makes fixed max-height only regardless the screen height)
    // placed a new issue in Git, see if we get any response.
    self.$nextTick(() => {
      window.addEventListener('resize', () => {
        // debugger
        self.btableMaxHeight = (window.innerHeight - 150).toString() + 'px' // where offset is some kind of constant margin you need from the top
      })
    })
  },
  beforeDestroy() {
    window.removeEventListener('resize', () => {})
  }
}
</script>

<style scoped>

</style>
