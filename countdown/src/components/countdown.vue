<template>
  <div class="main">
    <h1 class="heading">Countdown</h1>
    <div id="countdown">
      <p>{{ demo }}</p>
    </div>
  </div>
</template>
<script>
export default {
  name: "count-down",
  data() {
    return {
      demo: "",
    };
  },
  methods: {
    countdown() {
      var dest = new Date("dec 31 , 2023 10:00:00").getTime();

      var x = setInterval(function () {
        var now = new Date().getTime();

        var diff = dest - now;

        var day = Math.floor(diff / (1000 * 60 * 60 * 24));
        console.log("days =", day);

        var hours = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60 * 60));
        console.log("hours =", hours);

        var minus = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        console.log("minus =", minus);

        var seconds = Math.floor((diff % (1000 * 60)) / 1000);
        console.log("seconds =", seconds);

        document.getElementById("countdown").innerHTML =
          day +
          " Days, " +
          hours +
          " Hours, " +
          minus +
          " Minutes, " +
          seconds +
          " seconds ";

        console.log(x);
      }, 1000);
    },
  },
  mounted() {
    this.countdown();
  },
};
</script>
<style>
.main{
  background-color: beige;
}
#countdown {
  width: max-content;
  height: auto;
  border-radius: 10px;
  font-size: 3vw;
  margin-left: 20rem;
  color: white;
  background-color: rgb(235, 50, 50);
}
.heading {
  width: max-content;
  border-radius: 10px;
  color: white;
  font-size: 5em;
  margin-top: 10rem;
  margin-left: 28rem;


  background-color: rgb(235, 50, 130);
}

</style>
