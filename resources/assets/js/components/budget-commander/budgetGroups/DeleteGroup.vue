<template>
  <div class="col w-full">
    <div class="card empty">
      <div class="flex">
        <div class="mx-auto mt-24">
          <p class="card-title">You're about to delete budget group "{{selectedGroup.budget_group_name}}"</p>
          <div class="flex justify-between mt-12 mb-6">
            <button 
              class="red-primary"
              @click="$emit('deleteSelectedGroup', selectedGroup.budget_group_id)"
            >
              Are you sure?
            </button>
            <button 
              class="gray-secondary"
              @click="$emit('backToEditList')"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "DeleteGroup",
  props: {
    selectedGroup: {
      type: Object,
      required: true
    }
  }
}
</script>

<style scoped>

</style>