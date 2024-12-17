<template>
  <div class="todo-container">
    <h2 class="title">My To-Do List</h2>

    <form @submit.prevent="addTask" class="task-form">
      <input
        v-model="newTask"
        type="text"
        placeholder="Add a new task"
        required
        class="task-input"
      />
      <button type="submit" class="add-task-button">Add Task</button>
    </form>

    <ul class="task-list">
      <li v-for="task in tasks" :key="task.id" class="task-item">
        <input
          type="checkbox"
          v-model="task.completed"
          @change="updateTask(task)"
          class="task-checkbox"
        />
        <span
          :class="{ completed: task.completed }"
          class="task-text"
        >
          <span v-if="!task.editing">{{ task.task }}</span>
          <input
            v-else
            v-model="task.task"
            type="text"
            class="task-input-edit"
            @blur="saveTask(task)"
            @keyup.enter="saveTask(task)"
          />
        </span>
        <button @click="deleteTask(task.id)" class="delete-button">
          <i class="fas fa-trash-alt"></i>
        </button>
        <button v-if="!task.editing" @click="editTask(task)" class="edit-button">
          <i class="fa-solid fa-pen-to-square"></i>
        </button>
        <button v-if="task.editing" @click="saveTask(task)" class="save-button">
          <i class="fa-solid fa-floppy-disk"></i>
        </button>
      </li>
    </ul>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      tasks: [],
      newTask: "",
    };
  },
  mounted() {
    this.loadTasksFromLocalStorage();
    this.fetchTasks();
  },
  methods: {
    loadTasksFromLocalStorage() {
      const storedTasks = localStorage.getItem("tasks");
      if (storedTasks) {
        this.tasks = JSON.parse(storedTasks);
      }
    },
    saveTasksToLocalStorage() {
      localStorage.setItem("tasks", JSON.stringify(this.tasks));
    },
    fetchTasks() {
      axios
        .get("/api/tasks")
        .then((response) => {
          this.tasks = response.data;
          this.saveTasksToLocalStorage();
        })
        .catch((error) => {
          console.error("Error fetching tasks:", error);
        });
    },
    isValidTask(task) {
      const regex = /^[a-zA-Z0-9\s]+$/;
      return regex.test(task);
    },
    addTask() {
      if (!this.newTask.trim()) {
        alert("Task cannot be empty!");
        return;
      }

      if (!this.isValidTask(this.newTask)) {
        alert(
          "Task contains invalid characters! Only letters, numbers, and spaces are allowed."
        );
        return;
      }

      const newTask = {
        id: Date.now(),
        task: this.newTask,
        completed: false,
        editing: false,
      };

      this.tasks.push(newTask);
      this.saveTasksToLocalStorage();
      this.newTask = "";

      axios
        .post("/api/tasks", { task: newTask.task })
        .then((response) => {
          newTask.id = response.data.id;
          this.saveTasksToLocalStorage();
        })
        .catch((error) => {
          console.error("Error adding task to backend:", error);
        });
    },
    editTask(task) {
      task.editing = true;
    },
    saveTask(task) {
      if (!this.isValidTask(task.task)) {
        alert(
          "Task contains invalid characters! Only letters, numbers, and spaces are allowed."
        );
        return;
      }

      task.editing = false;
      this.saveTasksToLocalStorage();

      if (task.id) {
        axios
          .patch(`/api/tasks/${task.id}`, {
            task: task.task,
            completed: task.completed,
          })
          .catch((error) => {
            console.error("Error updating task:", error);
          });
      }
    },
    updateTask(task) {
      task.editing = false;
      this.saveTasksToLocalStorage();

      if (task.id) {
        axios
          .patch(`/api/tasks/${task.id}`, {
            task: task.task,
            completed: task.completed,
          })
          .catch((error) => {
            console.error("Error updating task:", error);
          });
      }
    },
    deleteTask(id) {
      this.tasks = this.tasks.filter((task) => task.id !== id);
      this.saveTasksToLocalStorage();

      axios
        .delete(`/api/tasks/${id}`)
        .catch((error) => {
          console.error("Error deleting task:", error);
        });
    },
  },
};
</script>
<style scoped>
.todo-container {
  max-width: 600px;
  margin: 40px auto;
  padding: 25px;
  background: #121212; /* Black background */
  border-radius: 12px;
  box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.5); /* Subtle shadow for depth */
}

.title {
  font-size: 2.2rem;
  font-weight: 600;
  color: #f5f5f5; /* White for title */
  text-align: center;
  margin-bottom: 1.5rem;
}

.task-form {
  display: flex;
  gap: 0.8rem;
}

.task-input {
  flex-grow: 1;
  padding: 10px 15px;
  border: 2px solid #333; /* Dark gray border */
  border-radius: 8px;
  font-size: 1rem;
  background: #1e1e1e; /* Darker input background */
  color: #f5f5f5; /* White text */
  transition: border-color 0.3s ease;
}

.task-input:focus {
  border-color: #909090; /* Light gray border on focus */
  outline: none;
}

.add-task-button {
  padding: 10px 15px;
  font-size: 1rem;
  font-weight: 500;
  background: #333; /* Dark gray button */
  color: #f5f5f5; /* White text */
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.add-task-button:hover {
  background: #505050; /* Lighter gray on hover */
}

.task-list {
  margin-top: 20px;
  padding: 0;
  list-style: none;
}

.task-item {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  padding: 10px 15px;
  background: #1e1e1e; /* Dark background for task items */
  border: 1px solid #333; /* Dark gray border */
  border-radius: 8px;
  transition: background 0.3s ease;
  margin-bottom: 10px;
}

.task-item:hover {
  background: #262626; /* Slightly lighter on hover */
}

.task-text {
  flex-grow: 1;
  font-size: 1rem;
  color: #f5f5f5; /* White text */
  transition: color 0.3s ease;
}

.completed {
  text-decoration: line-through;
  color: #909090; /* Light gray for completed tasks */
}

.delete-button,
.edit-button,
.save-button {
  background: none;
  border: none;
  font-size: 1.2rem;
  color: #909090; /* Gray for icons */
  cursor: pointer;
  transition: color 0.3s ease;
}

.delete-button:hover {
  color: #f44336; /* Red on hover */
}

.edit-button:hover,
.save-button:hover {
  color: #4caf50; /* Green on hover */
}

</style>
