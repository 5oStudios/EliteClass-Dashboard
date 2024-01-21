<!-- QuestionComponent.vue -->
<template>
  <div>
    <!-- Your HTML structure for the question form goes here -->
    <div>
      <label for="question">Question:</label>
      <textarea
        v-model="question"
        rows="6"
        placeholder="Enter Your Question"
        required
      ></textarea>
    </div>

    <div>
      <label for="type">Type:</label>
      <select v-model="selectedType" @change="handleTypeChange" required>
        <option value="none" disabled hidden>Select an Option</option>
        <option value="mcq">MCQ</option>
        <option value="audio">Audio</option>
        <option value="complete">Complete</option>
        <option value="image">Image</option>
        <option value="essay">Essay</option>
      </select>
    </div>

    <!-- Additional form fields go here -->

    <!-- Your submit button -->
    <div>
      <button @click="submitForm">Submit</button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      question: "",
      selectedType: "none",
      // Add additional data properties for other form fields
    };
  },
  methods: {
    handleTypeChange() {
      // Add logic to show/hide form fields based on the selected type
      // You can use this.selectedType to get the selected type
      console.log("Type changed:", this.selectedType);
    },
    submitForm() {
      // Add logic to submit the form data to the server
      console.log("Form submitted");
      // You can use this.question, this.selectedType, etc. to get form data
    },
  },
  created() {
    console.log("hi bye");
  },
};
</script>

<style scoped>
/* Your component-specific styles go here */
</style>
