<template>
  <el-row>
    <el-col>
      <el-card :body-style="{ padding: '0px' }">
        <div slot="header" class="clearfix">
          <span>Card name</span>
          <el-button style="float: right; padding: 3px 0" type="text">Operation button</el-button>
        </div>
        <img src="https://cdn.myanimelist.net/images/characters/14/72697.jpg?s=a9e8f30edd16226a70374fe4d0c5df1e" class="image">
        <div style="padding: 14px;">
          <span>Yummy hamburger</span>
          <div class="bottom clearfix">
            <time class="time">{{ currentDate }}</time>
            <el-button type="text" class="button">Operating</el-button>
          </div>
        </div>
      </el-card>
    </el-col>
  </el-row>
</template>

<script>
export default {
  name: "DCard",
  props: {
    anime: Object
  },
  data() {
    return {
      currentDate: new Date()
    };
  }
}
</script>

<style scoped>
.time {
  font-size: 13px;
  color: #999;
}

.bottom {
  margin-top: 13px;
  line-height: 12px;
}

.button {
  padding: 0;
  float: right;
}

.image {
  width: 100%;
  display: block;
}

.clearfix:before,
.clearfix:after {
  display: table;
  content: "";
}

.clearfix:after {
  clear: both
}
</style>