# **Oque é isso?**

Apenas um projeto básico feito com a intenção de aprender mais sobre a linguagem tal como alguns métodos/padrões utilizados na programação backend.

Adendo que tá só o caos esse negócio

![Negocio tá complicado](https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT_4wwLaF8XINRSZztjjpersfyi2MHirapm7KDX6JBgYw&s "Yamcha quem disse")

---

## **Se quiser testar**

Se quiser testar como tudo está quebrado. Basta iniciar o server na pasta public.

```bash
cd public
---
php -S localhost:8080
```

---

## **Rotas**

As rotas são declaradas de forma estática. E na seguinte ordem de pastas

```bash
cd routes/*/*.php
```

```php
Router::get('/rota/{params}',[
function(Request $req){
  (new Controller)->método($req)
  }]
);
```

---

### **Middewares**

São passados em um array de "_middlewares_" no mesmo array que o controlador.

**Exemplo**:

```php
use App\Http\Middlewares\Cache;
...

Router::get('/...',[
  'middlewares' => [
    Cache::class
  ], function(Request $req)...
]);
```

**Nota**

> Lembre-se de instanciar o middleware, caso não, o mesmo vai ser passado como string e não como uma classe.
