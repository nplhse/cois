<template>
  <div>
    <div id="age-area"></div>

    <b-table striped hover :items="this.data"></b-table>
  </div>
</template>

<script>
import * as c3 from 'c3';

export default {
  name: 'AgeDistributionPanel.vue',
  mounted() {
    var ageArea = c3.generate({
      bindto: '#age-area',
      data: {
        url: '/vuestats/api/age.json',
        mimeType: 'json',
        types: {
          male: 'area',
          female: 'area',
          other: 'area',
        },
        groups: [
            ['male', 'female', 'other']
          ],
      }
    });
  }
};
</script>

<style scoped>

</style>
