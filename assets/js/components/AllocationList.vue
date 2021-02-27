<template>
    <div>
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
        >
            <template #table-busy>
                <div class="text-center my-2">
                    <b-spinner class="align-middle" />
                    <strong>Loading...</strong>
                </div>
            </template>

            <template #cell(id)="data">
                <b><a :href="data.item.id">{{ data.value }}</a></b>
            </template>

            <template #cell(hospital)="data">
                <a :href="data.value">{{ data.value }}</a>
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
                    v-if="data.item.sK === '1'"
                    class="text-danger font-weight-bold"
                >
                    <b>SK1</b>
                </p>
                <p
                    v-if="data.item.sK === '2'"
                    class="text-warning font-weight-bold"
                >
                    <b>SK2</b>
                </p>
                <p
                    v-if="data.item.sK === '3'"
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
                >P+ </b>
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
                        :href="row.item.id"
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
            perPage: 10,
            currentPage: 1,
            pageOptions: [10, 25, 50, 100],
            loading: true,
            fields: [
                { key: 'id', label: 'ID', thStyle: 'width: 10em' },
                { key: 'dispatchArea', label: 'Dispatch Area', thStyle: 'width: 25em' },
                { key: 'hospital', label: 'Hospital', thStyle: 'width: 20em' },
                { key: 'times', label: 'Times', thStyle: 'width: 20em' },
                { key: 'person', label: 'Pat.', thStyle: 'width: 10em' },
                { key: 'urgency', label: 'SK', thStyle: 'width: 10em' },
                { key: 'dispatch', label: 'Dispatch', thStyle: 'width: 50em' },
                { key: 'occasion', label: 'Occasion', thStyle: 'width: 25em' },
                { key: 'assignment', label: 'Assignment', thStyle: 'width: 20em' },
                { key: 'properties', label: 'Properties' },
                { key: 'actions', label: 'Actions', thStyle: 'width: 20em' },
            ],
            items: [],
        };
    },
    watch: {
        currentPage() {
            this.loadAllocations();
        },
        perPage() {
            this.loadAllocations();
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
