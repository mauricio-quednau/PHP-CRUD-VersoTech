
let coresDisponiveis = [];
function criarSelectCor() {
    const div = document.createElement('div');
    div.classList.add('input-group', 'mb-2');

    const select = document.createElement('select');
    select.name = "cores[]";
    select.classList.add("form-select");
    select.required = false;

    // Adiciona a opção inicial
    select.innerHTML = `<option value="">Selecione uma cor</option>`;

    // Popula as cores carregadas do backend
    coresDisponiveis.forEach(cor => {
        const opt = document.createElement("option");
        opt.value = cor.id;
        opt.textContent = cor.name;
        select.appendChild(opt);
    });

    // Botão remover
    const btn = document.createElement('button');
    btn.type = "button";
    btn.classList.add("btn", "btn-danger", "remove-cor");
    btn.textContent = "X";
    btn.onclick = () => div.remove();

    div.appendChild(select);
    div.appendChild(btn);

    return div;
}

async function listarCores() {
    try {
        const payload = {
            funcao: "listar",
        };

        const resp = await fetch("app/handlers/color.handler.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(payload)});

        let data = await resp.json();
        if (data.sucesso) {
            coresDisponiveis = data.cores;
            document.getElementById('coresContainer').innerHTML = "";
            document.getElementById('coresContainer').appendChild(criarSelectCor());
        }

    } catch (err) {
        console.error("Erro ao carregar cores:", err);
    }
}

document.getElementById('usuarioModal').addEventListener('show.bs.modal', async function () {
    if (coresDisponiveis.length === 0) {
        listarCores();
    }
});

document.getElementById('usuarioModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('usuarioForm').reset();
    document.getElementById('coresContainer').innerHTML = '';
    document.getElementById('coresContainer').appendChild(criarSelectCor());
});

// Adiciona novos campos de cor
document.getElementById('addCor').addEventListener('click', function () {
    document.getElementById('coresContainer').appendChild(criarSelectCor());
});

// Remove campo de cor
document.addEventListener('click', function (e) {
    if (e.target && e.target.classList.contains('remove-cor')) {
        e.target.parentElement.remove();
    }
});

// Captura do form (salvar - inserção ou alteração)
document.getElementById('usuarioForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    //console.log(Object.fromEntries(formData.entries())); // Nome e Email
    //console.log(formData.getAll('cores[]')); // Todas as cores vinculadas
    let userId = document.getElementById('usuario_id').value;
    let funcao = userId ? 'atualizar' : 'inserir';
    const payload = {
        funcao: funcao,
        usuario_id: userId,
        nome: document.getElementById('nome').value,
        email: document.getElementById('email').value,
        cores: formData.getAll('cores[]')
    };
    //console.log(payload);
    const resp = await fetch("app/handlers/user.handler.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(payload)});

    let data = await resp.json();
    if (data.sucesso) {
        alert(data.mensagem);
        window.location.reload();
    }
});


async function listarUsuarios() {
    try {
        const payload = {
            funcao: "listar",
        };

        const resp = await fetch("app/handlers/user.handler.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(payload)});

        let data = await resp.json();
        if (data.sucesso) {
            let usuarios = data.usuarios;
            const tbody = document.querySelector("#usuariosTable tbody");
            tbody.innerHTML = "";

            usuarios.forEach(user => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
        <td>${user.id}</td>
        <td>${user.nome}</td>
        <td>${user.email}</td>
        <td>${user.cores.length ? user.cores.join(", ") : "-"}</td>
        <td>
          <button class="btn btn-sm btn-warning me-1" onclick="editar(${user.id})">Editar</button>
          <button class="btn btn-sm btn-danger" onclick="excluir(${user.id})">Excluir</button>
        </td>
      `;
                //<td>${user.colors ? user.colors.join(", ") : "-"}</td>
                tbody.appendChild(tr);
            });
        }
    } catch (err) {
        console.error("Erro ao carregar usuários:", err);
    }
}
document.addEventListener("DOMContentLoaded", listarUsuarios);

async function editar(id) {
    if (coresDisponiveis.length === 0) {
        listarCores();
    }

    const payload = {
        funcao: "getUsuario",
        usuario_id: id
    };

    try {
        const response = await fetch("app/handlers/user.handler.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(payload)});

        const data = await response.json();

        if (data.sucesso) {
            const usuario = data.usuario;

            // Preencher campos do modal
            document.getElementById('usuario_id').value = usuario.id;
            document.getElementById('nome').value = usuario.name;
            document.getElementById('email').value = usuario.email;

            preencherCoresUsuario(usuario, coresDisponiveis);

            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById('usuarioModal'));
            modal.show();
        } else {
            alert(data.mensagem);
        }
    } catch (err) {
        console.error("Erro ao carregar usuário:", err);
    }
}

function preencherCoresUsuario(usuario, listaCores) {
    const coresContainer = document.getElementById('coresContainer');
    coresContainer.innerHTML = ''; // limpa o container

    // Se não houver cores, cria um select vazio
    const cores = usuario.cores.length > 0 ? usuario.cores : [''];

    cores.forEach(corId => {
        const div = document.createElement('div');
        div.classList.add('input-group', 'mb-2');

        div.innerHTML = `
            <select name="cores[]" class="form-select" required>
                <option value="">Selecione uma cor</option>
                ${listaCores.map(c =>
                `<option value="${c.id}" ${c.id == corId ? 'selected' : ''}>${c.name}</option>`
        ).join('')}
            </select>
            <button type="button" class="btn btn-danger remove-cor">X</button>
        `;

        coresContainer.appendChild(div);
    });
}


async function excluir(id) {
    if (confirm('Realmente excluir?')) {
        const payload = {
            funcao: "excluir",
            usuario_id: id
        };

        const resp = await fetch("app/handlers/user.handler.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(payload)});

        let data = await resp.json();
        if (data.sucesso) {
            alert(data.mensagem);
            window.location.reload();
        } else {
            alert('Erro');
        }
    }
}