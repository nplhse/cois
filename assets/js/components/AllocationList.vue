<template>
    <div>
      <div class="float-right">
        <b-button-group>
          <b-button v-b-toggle.sidebar-1 variant="primary">Toggle Filters</b-button>
        </b-button-group>
      </div>

      <b-sidebar id="sidebar-1" title="Filters" shadow>
        <template #footer="{ hide }">
          <div class="d-flex bg-dark text-light align-items-right px-3 py-2">
            <b-button size="sm" @click="hide">Close</b-button>
          </div>
        </template>

        <div class="px-3 py-2">
          <b-card title="by Keyword" class="mb-2">
              <b-row>
                <b-col>
                  <b-form-group
                      label="Search"
                      label-for="filter-input"
                      label-cols-sm="3"
                      label-align-sm="right"
                      label-size="sm"
                      class="mb-0"
                  >
                    <b-input-group size="sm">
                      <b-form-input
                          id="filter-input"
                          v-model="filters.search"
                          type="search"
                          placeholder="Type to Search"
                      />

                      <b-input-group-append>
                        <b-button
                            :disabled="!filters.search"
                            @click="filters.search = ''"
                        >
                          X
                        </b-button>
                      </b-input-group-append>
                    </b-input-group>
                  </b-form-group>
                </b-col>
              </b-row>
              <b-row>
                <b-col>
                  <b-form-group
                      v-slot="{ ariaDescribedby }"
                      v-model="filters.fields"
                      label="Filter On"
                      label-cols-sm="3"
                      label-align-sm="right"
                      label-size="sm"
                      class="mb-0"
                  >
                    <b-form-checkbox-group
                        v-model="filters.fields"
                        :aria-describedby="ariaDescribedby"
                        class="mt-1"
                    >
                      <b-form-checkbox value="supplyArea">
                        &nbsp;Supply Area
                      </b-form-checkbox>
                      <b-form-checkbox value="dispatchArea">
                        &nbsp;Dispatch Area
                      </b-form-checkbox>
                      <b-form-checkbox value="hospital.name">
                        &nbsp;Hospital Name
                      </b-form-checkbox>
                      <b-form-checkbox value="RMI">
                        &nbsp;RMI
                      </b-form-checkbox>
                      <b-form-checkbox value="PZCText">
                        &nbsp;PZC Text
                      </b-form-checkbox>
                      <b-form-checkbox value="assignment">
                        &nbsp;Assignment
                      </b-form-checkbox>
                      <b-form-checkbox value="occasion">
                        &nbsp;Occasion
                      </b-form-checkbox>
                    </b-form-checkbox-group>
                  </b-form-group>
                </b-col>
              </b-row>
          </b-card>

          <b-card title="by Date" class="mb-2">
              <label for="after-datepicker">After</label>
              <b-form-datepicker
                  id="after-datepicker"
                  v-model="filters.dateAfter"
                  class="mb-2"
                  reset-button
                  close-button
              ></b-form-datepicker>

              <label for="before-datepicker">Before</label>
              <b-form-datepicker
                  id="before-datepicker"
                  v-model="filters.dateBefore"
                  class="mb-2"
                  reset-button
                  close-button
              ></b-form-datepicker>
          </b-card>

          <b-card title="by Properties" class="mb-2">
              <b-form-checkbox v-model="filters.properties.requiresResus" name="check-button" switch>
                  &nbsp;Requires Resus (S+)
              </b-form-checkbox>
              <b-form-checkbox v-model="filters.properties.requiresCathlab" name="check-button" switch>
                  &nbsp;Requires Cathlab (H+)
              </b-form-checkbox>
              <b-form-checkbox v-model="filters.properties.isWithPhysician" name="check-button" switch>
                  &nbsp;Is with Physician (N+)
              </b-form-checkbox>
              <b-form-checkbox v-model="filters.properties.isCPR" name="check-button" switch>
                  &nbsp;Is CPR (R+)
              </b-form-checkbox>
              <b-form-checkbox v-model="filters.properties.isVentilated" name="check-button" switch>
                  &nbsp;Is Ventilated (B+)
              </b-form-checkbox>
              <b-form-checkbox v-model="filters.properties.isShock" name="check-button" switch>
                  &nbsp;Is Shock
              </b-form-checkbox>
              <b-form-checkbox v-model="filters.properties.isPregnant" name="check-button" switch>
                  &nbsp;Is Pregnant
              </b-form-checkbox>
              <b-form-checkbox v-model="filters.properties.isWorkAccident" name="check-button" switch>
                  &nbsp;Is Work Accident (BG+)
              </b-form-checkbox>
          </b-card>
        </div>
      </b-sidebar>

        <b-table
            id="allocation-table"
            striped
            hover
            :sticky-header="btableMaxHeight"
            :no-border-collapse="noBorderCollapse"
            :responsive="true"
            :busy="loading"
            :items="items"
            :fields="fields"
            :loading="loading"
            :no-local-sorting="true"
            :label-sort-asc="null"
            :label-sort-desc="null"
            :label-sort-clear="null"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            sort-icon-right
            :filter="null"
        >
            <template #table-busy>
                <div class="text-center my-2">
                    <b-spinner class="align-middle" />
                    <strong>Loading...</strong>
                </div>
            </template>

            <template #cell(id)="data">
                <b><a :href="data.item.id.toString()">{{ data.value }}</a></b>
            </template>

            <template #cell(hospital)="data">
                <a :href="hospitalLinks[data.item.hospital].toString()">{{ hospitals[data.item.hospital] }}</a>
            </template>

            <template #cell(times)="data">
                {{ data.item.createdAt | formatDate }}<br>
                {{ data.item.arrivalAt | formatDate }}
            </template>

            <template #cell(person)="data">
                {{ data.item.gender }}<br>
                {{ data.item.age }}
            </template>

            <template #cell(urgency)="data">
                <p
                    v-if="data.item.SK === 'SK1'"
                    class="text-danger font-weight-bold"
                >
                    <b>SK1</b>
                </p>
                <p
                    v-if="data.item.SK === 'SK2'"
                    class="text-warning font-weight-bold"
                >
                    <b>SK2</b>
                </p>
                <p
                    v-if="data.item.SK === 'SK3'"
                    class="text-success font-weight-bold"
                >
                    <b>SK3</b>
                </p>
            </template>

            <template #cell(dispatch)="data">
                <b>{{ data.item.speciality }}</b><br>
                {{ data.item.rMI }} {{ data.item.pZCText }}
            </template>

            <template #cell(properties)="data">
                <b
                    v-if="data.item.requiresResus"
                    class="text-danger"
                >S+</b>
                <b
                    v-if="data.item.requiresCathlab"
                    class="text-danger"
                >H+ </b>
                <b
                    v-if="data.item.isWithPhysician"
                    class="text-danger"
                >N+ </b>
                <b
                    v-if="data.item.isCPR"
                    class="text-danger"
                >R+ </b>
                <b
                    v-if="data.item.isVentilated"
                    class="text-danger"
                >B+ </b>
                <b
                    v-if="data.item.isPregnant"
                    class="text-warning"
                >Pregnant</b>
                <b
                    v-if="data.item.isWorkAccident"
                    class="text-danger"
                >BG+ </b>
            </template>

            <template #cell(actions)="row">
                <b-button-group>
                    <b-button
                        size="sm"
                        variant="secondary"
                        @click="row.toggleDetails"
                    >
                        Details
                    </b-button>
                    <b-button
                        size="sm"
                        :href="row.item.id.toString()"
                        variant="primary"
                    >
                        View
                    </b-button>
                </b-button-group>
            </template>

            <template #row-details="row">
                <b-card>
                    <b-row class="mb-3">
                        <b-col><b>Secondary RMI</b><br>{{ row.item.secondaryPZC }}</b-col>
                        <b-col><b>Secondary Diagnosis</b><br>{{ row.item.secondaryPZCText }}</b-col>
                        <b-col><b>Speciality detail</b><br>{{ row.item.specialityDetail }}</b-col>
                        <b-col>
                            <b>Speciality was closed?</b><br>
                            <template v-if="row.item.specialityWasClosed">
                                <p class="text-warning font-weight-bold">
                                    Yes (Closed)
                                </p>
                            </template>
                            <template v-else>
                                <p class="text-success">
                                    No (Open)
                                </p>
                            </template>
                        </b-col>
                    </b-row>

                    <b-row>
                        <b-col>
                            <b>Infectious</b><br>
                            <template v-if="row.item.isInfectious !== 'Keine'">
                                <b class="text-warning">{{ row.item.isInfectious }}</b>
                            </template>
                            <template v-else>
                                {{ row.item.isInfectious }}
                            </template>
                        </b-col>
                        <b-col><b>Handover point</b><br>{{ row.item.handoverPoint }}</b-col>
                        <b-col><b>Mode of transport</b><br>{{ row.item.modeOfTransport }}</b-col>
                        <b-col><b>Transport comment</b><br><i>{{ row.item.comment }}</i></b-col>
                    </b-row>
                </b-card>
            </template>
        </b-table>

        <b-row>
            <b-col class="pb-2">
                <div>
                    Showing <b>{{ perPage }}</b> of <b>{{ totalItems }}</b> allocations.
                    Sorting By: <b>{{ sortBy }}</b>, Sort Direction:
                    <b>{{ sortDesc ? 'Descending' : 'Ascending' }}</b>
                </div>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-form-group>
                    <label
                        class="mr-sm-2"
                        for="pagination-selector"
                    >Items per Page</label>
                    <b-form-select
                        id="pagination-selector"
                        v-model="perPage"
                        :options="pageOptions"
                        size="sm"
                    />
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
                />
            </b-col>
        </b-row>
    </div>
</template>

<script>
import { fetchAllocations } from '../service/allocation-service';

export default {
    name: 'AllocationList',
    data() {
        return {
            btableMaxHeight: '600px',
            noBorderCollapse: true,
            totalItems: 0,
            sortBy: 'times',
            sortDesc: false,
            perPage: 10,
            currentPage: 1,
            pageOptions: [10, 25, 50, 100],
            filters: {
                search: null,
                fields: [],
                timeout: null,
                dateAfter: null,
                dateBefore: null,
                properties: {
                    requiresResus: false,
                    requiresCathlab: false,
                    isWithPhysician: false,
                    isCPR: false,
                    isVentilated: false,
                    isShock: false,
                    isPregnant: false,
                    isWorkAccident: false,
                },
            },
            loading: true,
            fields: [
                {
                    key: 'id', label: 'ID', thStyle: 'width: 10em', sortable: true,
                },
                {
                    key: 'dispatchArea', label: 'Dispatch Area', thStyle: 'width: 25em', sortable: true,
                },
                {
                    key: 'hospital', label: 'Hospital', thStyle: 'width: 20em', sortable: true,
                },
                {
                    key: 'times', label: 'Times', thStyle: 'width: 20em', sortable: true,
                },
                { key: 'person', label: 'Pat.', thStyle: 'width: 10em' },
                {
                    key: 'urgency', label: 'SK', thStyle: 'width: 10em', sortable: false,
                },
                { key: 'dispatch', label: 'Dispatch', thStyle: 'width: 50em' },
                { key: 'occasion', label: 'Occasion', thStyle: 'width: 25em' },
                { key: 'assignment', label: 'Assignment', thStyle: 'width: 20em' },
                { key: 'properties', label: 'Properties' },
                { key: 'actions', label: 'Actions', thStyle: 'width: 20em' },
            ],
            items: [],
        };
    },
    computed: {
        hospitals() {
            return window.hospitals;
        },
        hospitalLinks() {
            return window.hospitalLinks;
        },
    },
    watch: {
        currentPage() {
            this.loadAllocations();
        },
        perPage() {
            this.loadAllocations();
        },
        sortBy() {
            this.loadAllocations();
        },
        sortDesc() {
            this.loadAllocations();
        },
        'filters': {
            handler(newValue, oldValue) {
                this.loadAllocations();
            },
            deep: true
        },
    },
    created() {
        this.loadAllocations();
    },
    mounted() {
        const self = this;
        // to update b-table max-height to have a freeze header (sticky-header makes fixed max-height only regardless the screen height)
        // placed a new issue in Git, see if we get any response.
        self.$nextTick(() => {
            window.addEventListener('resize', () => {
                // debugger
                self.btableMaxHeight = `${(window.innerHeight - 150).toString()}px`; // where offset is some kind of constant margin you need from the top
            });
        });
    },
    beforeDestroy() {
        window.removeEventListener('resize', () => {});
    },
    methods: {
        async loadAllocations() {
            this.loading = true;

            let response;
            try {
                response = await fetchAllocations(this.currentPage, this.perPage, this.sortBy, this.sortDesc, this.filters);

                this.loading = false;
            } catch (e) {
                this.loading = false;

                return;
            }

            this.items = response.data['hydra:member'];
            this.totalItems = response.data['hydra:totalItems'];
        },
        filterAllocations(row, filter) {
            return true;
        },
    },
};
</script>

<style scoped>
  .smallCol {
    width: 250px;
  }
  .mediumCol {
    width: 500px;
  }
  .wideCol {
    width: 750px;
  }
</style>
