<template>
    <div>
        <b-button
            v-b-toggle.collapse-1
            variant="primary"
        >
            Toogle filters
        </b-button>

        <b-collapse
            id="collapse-1"
            class="mt-2"
        >
            <b-card>
                <b-row>
                    <b-col
                        lg="6"
                        class="my-1"
                    >
                        <b-form-group
                            label="Filter"
                            label-for="filter-input"
                            label-cols-sm="3"
                            label-align-sm="right"
                            label-size="sm"
                            class="mb-0"
                        >
                            <b-input-group size="sm">
                                <b-form-input
                                    id="filter-input"
                                    v-model="filter"
                                    type="search"
                                    placeholder="Type to Search"
                                />

                                <b-input-group-append>
                                    <b-button
                                        :disabled="!filter"
                                        @click="filter = ''"
                                    >
                                        Clear
                                    </b-button>
                                </b-input-group-append>
                            </b-input-group>
                        </b-form-group>
                    </b-col>

                    <b-col
                        lg="6"
                        class="my-1"
                    >
                        <b-form-group
                            v-slot="{ ariaDescribedby }"
                            v-model="filterOn"
                            label="Filter On"
                            label-cols-sm="3"
                            label-align-sm="right"
                            label-size="sm"
                            class="mb-0"
                        >
                            <b-form-checkbox-group
                                v-model="filterOn"
                                :aria-describedby="ariaDescribedby"
                                class="mt-1"
                            >
                                <b-form-checkbox value="supplyArea">
                                    Supply Area
                                </b-form-checkbox>
                                <b-form-checkbox value="dispatchArea">
                                    Dispatch Area
                                </b-form-checkbox>
                                <b-form-checkbox value="name">
                                    Name
                                </b-form-checkbox>
                                <b-form-checkbox value="size">
                                    Size
                                </b-form-checkbox>
                                <b-form-checkbox value="location">
                                    Location
                                </b-form-checkbox>
                            </b-form-checkbox-group>
                        </b-form-group>
                    </b-col>
                </b-row>
            </b-card>
        </b-collapse>

        <b-table
            id="hospital-table"
            striped
            hover
            responsive="true"
            :sticky-header="btableMaxHeight"
            no-border-collapse
            :busy="loading"
            :items="items"
            :fields="fields"
            :loading="loading"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            :label-sort-asc="null"
            :label-sort-desc="null"
            sort-icon-right
            :filter="filter"
            :filter-included-fields="filterOn"
            @filtered="onFiltered"
        >
            <template #table-busy>
                <div class="text-center my-2">
                    <b-spinner class="align-middle" />
                    <strong>Loading...</strong>
                </div>
            </template>

            <template #cell(name)="data">
                <b><a :href="data.item.id">{{ data.value }}</a></b>
            </template>

            <template #cell(size)="data">
                <b>{{ data.value }}</b> ({{ data.item.beds }} beds)
            </template>
        </b-table>

        <div>
            Showing <b>{{ totalItems }}</b> hospitals.
            Sorting By: <b>{{ sortBy }}</b>, Sort Direction:
            <b>{{ sortDesc ? 'Descending' : 'Ascending' }}</b>
        </div>
    </div>
</template>

<script>
import { fetchAllHospitals } from '../service/hospital-service';

export default {
    name: 'HospitalList',
    data() {
        return {
            btableMaxHeight: '500px',
            totalItems: 0,
            sortBy: 'supplyArea',
            sortDesc: false,
            loading: true,
            filter: null,
            filterOn: [],
            fields: [
                { key: 'id', label: 'ID', sortable: true },
                { key: 'dispatchArea', label: 'Dispatch Area', sortable: true },
                { key: 'supplyArea', label: 'Supply Area', sortable: true },
                { key: 'name', label: 'Name', sortable: true },
                { key: 'size', label: 'Size', sortable: true },
                { key: 'location', label: 'Location', sortable: true },
            ],
            items: [],
        };
    },
    watch: {
        currentPage() {
            this.loadHospitals();
        },
    },
    created() {
        this.loadHospitals();
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
        async loadHospitals() {
            this.loading = true;

            let response;
            try {
                response = await fetchAllHospitals();

                this.loading = false;
            } catch (e) {
                this.loading = false;

                return;
            }

            this.items = response.data['hydra:member'];
            this.totalItems = response.data['hydra:totalItems'];
        },
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length;
        },
    },
};
</script>

<style scoped>

</style>
