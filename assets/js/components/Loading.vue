<template>
  <div class="d-flex justify-content-center mb-3">
    <b-spinner label="Loading..."></b-spinner>
  </div>
</template>

<script>
export default {
  name: 'Loading',
};
</script>

<style lang="scss" module>
.component {
  background: url('../../images/loading.gif') no-repeat left center;
  padding: 0 0 4px 50px;
}
</style>
