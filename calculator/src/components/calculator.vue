<template>
  <div class="calculator">
    <div class="previous">Previous value is = {{ previous }}</div>
    <div class="display">{{ current }}</div>
    <div @click="clear" class="btn">C</div>
    <div @click="sign" class="btn">+/-</div>
    <div @click="percent" class="btn">%</div>
    <div @click="divide" class="btn operator">÷</div>
    <div @click="append(7)" class="btn">7</div>
    <div @click="append(8)" class="btn">8</div>
    <div @click="append(9)" class="btn">9</div>
    <div @click="times" class="btn operator">x</div>
    <div @click="append(4)" class="btn">4</div>
    <div @click="append(5)" class="btn">5</div>
    <div @click="append(6)" class="btn">6</div>
    <div @click="minus" class="btn operator">-</div>
    <div @click="append(1)" class="btn">1</div>
    <div @click="append(2)" class="btn">2</div>
    <div @click="append(3)" class="btn">3</div>
    <div @click="add()" class="btn operator">+</div>
    <div @click="append('0')" class="btn zero">0</div>
    <div @click="dot" class="btn">.</div>
    <div @click="equal" class="btn operator">=</div>
  </div>
</template>
<script>
export default {
  name: "calcu-lator",
  data() {
    return {
      current: "",
      previous: "",
    };
  },
  methods: {
    append(value) {
      this.current = this.current + value;
    },
    clear() {
      this.current = "";
      this.previous = "";
    },
    add() {
      this.previous  = this.current;
      this.current = "" + "+";
    },
    minus() {
      this.current = this.current + "-";
    },
    times() {
      this.current = this.current + "*";
    },
    divide() {
      this.current = this.current + "/";
    },
    equal() {
      let b = this.current.replace("+","")
      let c = Number(b) + Number(this.previous)
      this.current = c
      
    },
  },
};
</script>
<style scoped>
.calculator {
  border-radius: 10px;
  background-color: #a39d9d;
  margin: 10% 0 0 30%;
  width: 400px;
  font-size: 40px;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  grid-auto-rows: minmax(50px, auto);
}

.display {
  border-radius: px 10px 0 0;
  grid-column: 1 / 5;
  background-color: #333;
  color: white;
}
.previous {
  font-size: large;
  border-radius: 10px 10px 0 0;
  grid-column: 1 / 5;
  background-color: #333;
  color: white;
}

.zero {
  grid-column: 1 / 3;
}

.btn {
  background-color: #f2f2f2;
  border: 1px solid #999;
}

.operator {
  background-color: rgb(255, 98, 0);
  color: white;
}
</style>
