<template>
    <div>
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
            sort-icon-left
        >
            <template #table-busy>
                <div class="text-center my-2">
                    <b-spinner class="align-middle" />
                    <strong>Loading...</strong>
                </div>
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
            fields: [
                'id',
                'supplyArea',
                'name',
                'createdAt'],
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
    },
};
</script>

<style scoped>

</style>
