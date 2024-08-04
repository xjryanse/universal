<template>
  <div>
    <div class="amap-page-container">测试一下</div>
    <div>{{JSON.stringify(dataData)}}</div>
  </div>
</template>

<script>

  export default {
    name: 'pTest',
    props: {
      // 表单数据
      dataData: Object,
    }
  }
</script>


<style scoped>

</style>
