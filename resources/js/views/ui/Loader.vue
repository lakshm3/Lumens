<template>

<div class="lds-ring" :style="{'width': size+'px', 'height': size+'px'}">
  <div v-for="i in 4" :key="i" :style="{
    'width': (size*64/80)+'px', 
    'height': (size*64/80)+'px',
    'margin': (size*8/80)+'px',
    'border-width': (size*8/80)+'px',
    }"></div>
</div>

</template>

<script>
export default {
  props:["size"],
  
}
</script>
<style scoped>
.lds-ring {
  display: inline-block;
  position: relative;
  width: 80px;
  height: 80px;
}
.lds-ring div {
  box-sizing: border-box;
  display: block;
  position: absolute;
  width: 64px;
  height: 64px;
  margin: 8px;
  border: 8px solid #fff;
  border-radius: 50%;
  animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
  border-color: #fff transparent transparent transparent;
}
.lds-ring div:nth-child(1) {
  animation-delay: -0.45s;
}
.lds-ring div:nth-child(2) {
  animation-delay: -0.3s;
}
.lds-ring div:nth-child(3) {
  animation-delay: -0.15s;
}
@keyframes lds-ring {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
