<template>
    <div>
        <b-table id="rmi-table" striped hover :items="this.data.rmi">
            <template #table-busy>
                <div class="text-center my-2">
                    <b-spinner class="align-middle" />
                    <strong>Loading...</strong>
                </div>
            </template>
        </b-table>

        <div>
            Showing <b>{{ totalItems }}</b> hospitals. Sorting By:
            <b>{{ sortBy }}</b
            >, Sort Direction:
            <b>{{ sortDesc ? "Descending" : "Ascending" }}</b>
        </div>
    </div>
</template>

<script>
export default {
    name: "RMITable.vue",
    props: ["data"],
};
</script>

<style scoped></style>
