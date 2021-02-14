<template>
  <div>
    <div class="col-12">
      <div class="mt-4">
        <Loading v-show="loading" />

        <h5
            v-show="!loading && items.length === 0"
            class="ml-4"
        >
          Sorry, no allocations found!
        </h5>
      </div>
    </div>

    <b-table
        id="allocation-table"
        v-show="!loading"
        striped
        hover
        responsive="true"
        sticky-header="true"
        no-border-collapse
        :items="items"
        :fields="fields"
        :loading="loading"
    >
    </b-table>

    <b-row>
      <b-col>
        <label class="mr-sm-2" for="pagination-selector">Items per Page</label>
        <b-form-select id="pagination-selector" v-model="perPage" :options="pageOptions"></b-form-select>
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
import axios from 'axios';
import Loading from "./Loading";
import { fetchAllocations } from '../service/allocation-service';

export default {
  name: "AllocationList",
  components: {
    Loading
  },
  data() {
    return {
      totalItems: 0,
      perPage: 10,
      currentPage: 1,
      pageOptions: [10, 25, 50, 100],
      loading: true,
      fields: ['id', 'dispatchArea', 'supplyArea', 'hospital', 'createdAt'],
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
  }
}
</script>

<style scoped>

</style>
