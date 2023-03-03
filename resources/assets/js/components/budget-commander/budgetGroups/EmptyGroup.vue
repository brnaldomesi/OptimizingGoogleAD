<template>
  <div class="col w-full">
    <div class="card empty">
      <div class="flex">
        <div class="mx-auto mt-24">
          <p class="card-title">You don't have any Budget Groups</p>
          <p>Do you need Budget Groups? Watch the video tutorial to <a class="text-redPrimary underline cursor-pointer" href="#createGroupBtn" @click="$emit('showVideoModal')">learn more.</a> Or click the button below to get started.</p>
          <div class="flex justify-center mt-12 mb-6">
            <button 
              class="red-primary"
              @click="$emit('updateGroupState', 'create')"
              id="createGroupBtn"
            >
              Create Budgets
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "EmptyGroup"
}
</script>

<style scoped>

</style>