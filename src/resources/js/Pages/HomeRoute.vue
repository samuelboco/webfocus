<script setup>
import axios from "axios";
import { ref } from "vue";

const users = ref();
const inputId = ref();
const inputName = ref();
const inputEmail = ref();
const inputPassword = ref();
const addUserErr = ref();
const editUserErr = ref();

const getValue = async () => {
    try {
        users.value = await axios.get("/api/user/index");
    } catch (error) {
        console.log(error);
    }
};
getValue();

function toggleViewModal(modalElem = "add-user") {
    let modal = document.getElementById(modalElem);
    modal.style.display === "none" ? modal.style.display = "block" : modal.style.display = "none";
    document.getElementById('input-name').value = '';
    document.getElementById('input-email').value = '';
    document.getElementById('input-password').value = '';
    document.getElementById('input-confirm').value = '';
    addUserErr.value = null;
    editUserErr.value = null;
}

async function addNewUser() {
    await axios.post('/api/user/store', {
        name: document.getElementById('input-name').value,
        email: document.getElementById('input-email').value,
        password: document.getElementById('input-password').value,
        password_confirmation: document.getElementById('input-confirm').value

    }).then(function (response) {
        getValue();
        toggleViewModal();
    }).catch(function (error) {
        let errs = [];
        for (const [key, value] of Object.entries(error.response.data.errors)) {
            errs.push(value);
        }
        addUserErr.value = errs;
    });

}

async function deleteUser(userId) {
    await axios.post(`/api/user/destroy`, {
        id: userId,
    }).then(function (response) {
        console.log(response);
    }).catch(function (error) {
        console.log(error);
    });
    getValue();
}

async function editUser() {
    await axios.post('/api/user/update', {
        id: inputId.value,
        name: inputName.value,
        email: inputEmail.value
    }).then(function (response) {
        getValue();
        toggleViewModal('edit-user');
    }).catch(function (error) {
        let errs = [];
        for (const [key, value] of Object.entries(error.response.data.errors)) {
            errs.push(value);
        }
        editUserErr.value = errs;
    });

}

</script>


<template>
    <div class="container">
        <h1>Users</h1>
        <div style="text-align: right;"><button type="button" class="btn btn-primary" @click="toggleViewModal()">Add New
                User</button></div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email Address</th>
                    <th scope="col">Created At</th>
                    <th scope="col">Updated At</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="users" v-for="user in users.data">
                    <td>{{ user.id }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.created_at }}</td>
                    <td>{{ user.updated_at }}</td>
                    <td>
                        <button
                            @click="inputId = user.id; inputName = user.name; inputEmail = user.email; toggleViewModal('edit-user')"
                            type="button" class="btn btn-outline-warning" style="margin-right: 10px;">Edit</button>
                        <button @click="deleteUser(user.id)" type="button"
                            class="btn btn-outline-danger">Delete</button>
                    </td>
                </tr>
                <tr v-else>
                    <td colspan="5" class="table-active">No Users Found</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="modal" tabindex="-1" id="add-user" style="display: none">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button @click="toggleViewModal()" type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="input-name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="input-name" placeholder="John Doe">
                    </div>
                    <div class="mb-3">
                        <label for="input-email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="input-email" placeholder="name@example.com">
                    </div>
                    <label for="input-password" class="form-label">Password</label>
                    <input type="password" id="input-password" class="form-control"
                        aria-describedby="passwordHelpBlock">
                    <label for="input-confirm" class="form-label">Confirm Password</label>
                    <input type="password" id="input-confirm" class="form-control" aria-describedby="passwordHelpBlock">
                    <div v-if="addUserErr" class="alert alert-danger errors" role="alert" id="add-user-err"
                        style="margin-top: 5px">
                        <ul>
                            <li v-for="item in addUserErr">{{ item }}</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="toggleViewModal()" type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Close</button>
                    <button @click="addNewUser" type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="edit-user" style="display: none">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button @click="toggleViewModal('edit-user')" type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="input-name" class="form-label">Name</label>
                        <input v-model="inputName" type="text" class="form-control" id="input-name"
                            placeholder="John Doe">
                    </div>
                    <div class="mb-3">
                        <label for="input-email" class="form-label">Email address</label>
                        <input v-model="inputEmail" type="email" class="form-control" id="input-email"
                            placeholder="name@example.com">
                    </div>
                </div>
                <div v-if="editUserErr" class="alert alert-danger errors" role="alert" id="add-user-err"
                    style="margin-top: 5px">
                    <ul>
                        <li v-for="item in editUserErr">{{ item }}</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button @click="toggleViewModal('edit-user')" type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Close</button>
                    <button @click="editUser" type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

</template>