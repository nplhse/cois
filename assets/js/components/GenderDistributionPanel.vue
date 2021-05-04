<template>
  <div>
    <div id="gender-pie"></div>

    <b-table striped hover :items="this.data"></b-table>
  </div>
</template>

<script>
import * as c3 from "c3";

export default {
  name: "GenderDistributionPanel.vue",
  props: {
    data: {
      required: true,
      type: Array,
    },
  },
  data() {
    return {
      fields: ['gender', 'value'],
    };
  },
  mounted() {
    var genderPie = c3.generate({
      bindto: '#gender-pie',
      data: {
        url: '/vuestats/api/gender.json',
        mimeType: 'json',
        type: 'pie',
      }
    });
  }
}
</script>

<style lang="sass" scoped>

</style>
