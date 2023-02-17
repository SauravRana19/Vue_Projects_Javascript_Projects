<template>
  <div class="container">
    <h2>Todo list App</h2>
    <div class="row">
      <div class="col-md-8">
        <input
          type="text"
          class="form-control"
          placeholder="Enter your Value"
          id="task"
        />
      </div>
      <div class="col-md-4">
        <button
          class="btn btn-success"
          type="submit"
          @click="additem()"
        >
          Add
        </button>
      </div>
    </div>
    <div class="row">
      <div class="col-md-8">
        <ul class="list-group">
          <li class="list-group-item" id="b">
            {{ value }}<span class="removebtn">Remove</span>
          </li>
          <li>
            {{ data }}
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "tod-app",
  data() {
    return {
      value: [],
      data: "",
    };
  },
  methods: {
    additem() {
      let a = document.querySelector("#task").value;
      console.log("a value", a);
      this.value.push(a)
      localStorage.setItem("todoapp", this.value);
    },
  },
  mounted() {
    this.data = localStorage.getItem("todoapp");
  }
};
</script>
<style>
.removebtn {
  padding: 5px;
  color: white;
  border-radius: 5px;
  cursor: pointer;
  background-color: red;
  float: right;
  display: none;
}
li.list-group-item:hover > .removebtn {
  display: block;
}
</style>
