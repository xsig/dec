//var servidor="http://34.208.241.57";
var servidor="http://localhost";

function construirHeader(etiqueta,usuario, accion)
{
    var mensaje = { "mensaje_dec": { "header": {}, "mensaje": {} } };
    mensaje["mensaje_dec"]["header"]["usuario"] = usuario;
    mensaje["mensaje_dec"]["header"]["accion"] = accion;
    if(accion=="000004")
        mensaje["mensaje_dec"]["header"]["descripcion"] = "Autentificación Usuario - Password";
    if(accion=="10")
        mensaje["mensaje_dec"]["header"]["descripcion"] = "Usuario - Acciones Usuario";
    if(accion=="000001")
        mensaje["mensaje_dec"]["header"]["descripcion"]="Olvido de Clave";
    if(accion=="000002")
        mensaje["mensaje_dec"]["header"]["descripcion"]="Registro Nuevo Usuario - Selección de Empresas";
    mensaje["mensaje_dec"]["header"]["etiqueta"]=etiqueta;
    var d = new Date();
    mensaje["mensaje_dec"]["header"]["fecha"] = d.toJSON();

    return mensaje
}

function generarMensajeLogin(etiqueta,usuario, clave)
{
    mensaje = construirHeader(etiqueta,usuario, "000004");
    mensaje["mensaje_dec"]["mensaje"]["usuario"] = usuario;
    mensaje["mensaje_dec"]["mensaje"]["password"] = clave;

    return JSON.stringify(mensaje);
}

function generarMensajeFirma(etiqueta,usuario, empresa, codigoDoc, codigoFirma, rut, nombre, perfil, descripcion)
{
    mensaje = construirHeader(etiqueta,usuario, "10");
    if(empresa.length!=0)
        mensaje["mensaje_dec"]["header"]["empresa"] = empresa;

    mensaje["mensaje_dec"]["mensaje"]["codigoDocAcepta"] = codigoDoc;
    mensaje["mensaje_dec"]["mensaje"]["codigoFirma"] = codigoFirma;
    mensaje["mensaje_dec"]["mensaje"]["rutFirmante"] = rut;
    mensaje["mensaje_dec"]["mensaje"]["nombreFirmante"] = nombre;
    mensaje["mensaje_dec"]["mensaje"]["nombrePerfilFirmante"] = perfil;
    mensaje["mensaje_dec"]["mensaje"]["descripcionFirmante"] = descripcion;

    return JSON.stringify(mensaje);
}

function generarMensajeOlvidoClave(etiqueta,usuario)
{
    mensaje = construirHeader(etiqueta,usuario, "000001");
    mensaje["mensaje_dec"]["mensaje"]={};

    return JSON.stringify(mensaje);
}

function generarMensajeCambioClave(etiqueta,usuario, clave)
{
    mensaje = construirHeader(etiqueta,usuario, "000001");
    mensaje["mensaje_dec"]["mensaje"]["rut"]=usuario;
    mensaje["mensaje_dec"]["mensaje"]["password"]=clave;

    return JSON.stringify(mensaje);
}

function generarMensajeModificarUsuario(etiqueta,username, usuario)
{
    mensaje = construirHeader(etiqueta,username, "000002");
    mensaje["mensaje_dec"]["mensaje"]["rut"]=usuario.rut;
    if(usuario.nombre.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["nombre"]=usuario.nombre;
    if(usuario.segundoNombre.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["segundoNombre"]=usuario.segundoNombre;
    if(usuario.apellidoPaterno.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["apellidoPaterno"]=usuario.apellidoPaterno;
    if(usuario.apellidoMaterno.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["apellidoMaterno"]=usuario.apellidoMaterno;
    if(usuario.correoElectronico.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["correoElectronico"]=usuario.correoElectronico;
    if(usuario.genero.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["genero"]=usuario.genero;

    return JSON.stringify(mensaje);
}

function generarMensajeSolicitaEmpresas(etiqueta,username, rut, empresas)
{
    mensaje = construirHeader(etiqueta,username, "000002");
    mensaje["mensaje_dec"]["mensaje"]["rut"]=rut;
    mensaje["mensaje_dec"]["mensaje"]["empresasSolicitadas"]=empresas

    return JSON.stringify(mensaje);
}

function generarMensajeRegistrarUsuario(etiqueta,usuario)
{
    mensaje=construirHeader(etiqueta,usuario.rut,"000002");
    mensaje["mensaje_dec"]["mensaje"]["rut"]=usuario.rut;
    mensaje["mensaje_dec"]["mensaje"]["password"]=usuario.clave;
    if(usuario.nombre.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["Nombre"]=usuario.nombre;
    if(usuario.segundoNombre.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["segundoNombre"]=usuario.segundoNombre;
    if(usuario.apellidoPaterno.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["apellidoPaterno"]=usuario.apellidoPaterno;
    if(usuario.apellidoMaterno.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["apellidoMaterno"]=usuario.apellidoMaterno;
    if(usuario.correoElectronico.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["correoElectronico"]=usuario.correoElectronico;
    if(usuario.genero.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["genero"]=usuario.genero;
    if(usuario.empresas.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["empresasSolicitadas"]=usuario.empresas;
    return JSON.stringify(mensaje);
}

function generarMensajeConsultaGeneralUsuarios(etiqueta,usuario,empresa,rut,nombre,apellido,estado)
{
    mensaje=construirHeader(etiqueta,usuario,"10")
    mensaje["mensaje_dec"]["mensaje"]["empresa"]=empresa;
    if(rut.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["rutUsuario"] = rut;
    if(nombre.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["nombreUsuario"] = nombre;
    if(apellido.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["apellidoUsuario"] = apellido;
    if(estado.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["estado"] = estado;

    return JSON.stringify(mensaje);
}


function generarMensajeConsultaUsuarios(etiqueta,usuario,rut,nombre,apellido,estado)
{
    mensaje=construirHeader(etiqueta,usuario,"10")
    if(rut.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["rutUsuario"] = rut;
    if(nombre.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["nombreUsuario"] = nombre;
    if(apellido.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["apellidoUsuario"] = apellido;
    if(estado.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["estado"] = estado;

    return JSON.stringify(mensaje);
}

function generarMensajeConsultaTipoDocumentos(etiqueta,usuario,empresa)
{
    mensaje=construirHeader(etiqueta,usuario,"10")
    if(empresa.length!=0)
        mensaje["mensaje_dec"]["header"]["empresa"] = empresa;

    return JSON.stringify(mensaje);
}

function generarMensajeCrearSubtipoDocumentos(etiqueta,usuario,empresa,documento)
{
    mensaje=construirHeader(etiqueta,usuario,"10")
    if(empresa.length!=0)
        mensaje["mensaje_dec"]["header"]["empresa"] = empresa;
    mensaje["mensaje_dec"]["mensaje"]["nombre"]=documento.nombre;
    mensaje["mensaje_dec"]["mensaje"]["descripcion"]=documento.descripcion;
    mensaje["mensaje_dec"]["mensaje"]["codigo"]=documento.codigo;
    mensaje["mensaje_dec"]["mensaje"]["tipoDocumento"]=documento.tipoDocumento;
    mensaje["mensaje_dec"]["mensaje"]["firmantes"]=documento.firmantes;
    
    return angular.toJson(mensaje);
}

function generarMensajeConsultaDocumentos(etiqueta,usuario,empresa,tipo,subtipo,filtro)
{
    mensaje=construirHeader(etiqueta,usuario,"100")
    if(empresa.length!=0)
        mensaje["mensaje_dec"]["header"]["empresa"] = empresa;
    if(tipo!=null && tipo.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["tipoDocumento"] = tipo;
    if(subtipo!=null && subtipo.length!=0)
        mensaje["mensaje_dec"]["mensaje"]["subtipoDocumento"] = subtipo;
    if(filtro.estado!=null)
        mensaje["mensaje_dec"]["mensaje"]["estado"] = filtro.estado;

    return JSON.stringify(mensaje);
}

function generarMensajeConsultaRoles(etiqueta,usuario,empresa)
{
    mensaje=construirHeader(etiqueta,usuario,"10");
    mensaje["mensaje_dec"]["mensaje"]["empresa"] = empresa;
  
    return JSON.stringify(mensaje);
}

function generarMensajeConsultaPerfiles(etiqueta,usuario,empresa)
{
    mensaje=construirHeader(etiqueta,usuario,"10");
    mensaje["mensaje_dec"]["mensaje"]["empresa"] = empresa;
  
    return JSON.stringify(mensaje);
}

function generarMensajeIngresaPerfil(etiqueta,usuario,empresa,perfil)
{
    mensaje=construirHeader(etiqueta,usuario.rut,"10");
    mensaje["mensaje_dec"]["mensaje"]["empresa"] = empresa;
    mensaje["mensaje_dec"]["mensaje"]["nombrePerfil"] = perfil["nombrePerfil"];
    mensaje["mensaje_dec"]["mensaje"]["descripcionPerfil"] = perfil["descripcionPerfil"];
    mensaje["mensaje_dec"]["mensaje"]["roles"] = perfil["roles"];
    
    return JSON.stringify(mensaje);
}

function generarMensajeConsultaPerfilesEmpresa(etiqueta,usuario,empresa)
{
    mensaje=construirHeader(etiqueta,usuario,"10");
    empresas=[];
    empresas.push(empresa);
    mensaje["mensaje_dec"]["mensaje"]["Filtros"]=[];
    mensaje["mensaje_dec"]["mensaje"]["Filtros"].push({"empresas": empresas});
  
    return JSON.stringify(mensaje);
}

function generarMensajeAutorizaEmpresa(etiqueta,usuario,rut,empresa,accion)
{
    mensaje=construirHeader(etiqueta,usuario,"10");
    mensaje["mensaje_dec"]["mensaje"]["rutUsuario"] = rut;
    mensaje["mensaje_dec"]["mensaje"]["decisiones"]=[];
    mensaje["mensaje_dec"]["mensaje"]["decisiones"].push({"autoriza":accion,"empresa":empresa});
  
    return JSON.stringify(mensaje);
}

function generarMensajeAutorizaPerfil(etiqueta,usuario,rut,empresa,perfiles)
{
    mensaje=construirHeader(etiqueta,usuario,"10");
    mensaje["mensaje_dec"]["mensaje"]["rutUsuario"] = rut;
    mensaje["mensaje_dec"]["mensaje"]["decisiones"]=[];
    mensaje["mensaje_dec"]["mensaje"]["decisiones"].push({"autoriza":"SI","empresa":empresa, "perfiles":[]});
    for(i=0;i<perfiles.length;i++)
        mensaje["mensaje_dec"]["mensaje"]["decisiones"][0].perfiles.push({"id":perfiles[i].id,"nombrePerfil":perfiles[i].nombrePerfil});

    return JSON.stringify(mensaje);
}

function generarMensajeUpload(etiqueta,usuario, empresa, tipo, subtipo, nombre_archivo, descripcion, archivo,size)
{
    mensaje = construirHeader(etiqueta,usuario, "10");
    mensaje["mensaje_dec"]["header"]["empresa"] = empresa;
    mensaje["mensaje_dec"]["mensaje"]["TipoDocumentos"] = tipo;
    mensaje["mensaje_dec"]["mensaje"]["subTipoDocumentos"] = subtipo;

    var documentos=[];
    var documento={};
    documento["archivo"]=archivo;
    documento["nombre"]=nombre_archivo;
    documento["tamano"]=size;
    documento["usuarioCreador"]=usuario;
    documento["comentario"]=descripcion;
    documentos.push(documento);

    mensaje["mensaje_dec"]["mensaje"]["documentos"] = documentos;
  
    return JSON.stringify(mensaje);
}

function upper(text)
{
    text.value=text.value.toUpperCase();
}

function activarUpload()
{
    var deviceAgent = navigator.userAgent.toLowerCase();
    var agentID = deviceAgent.match(/(iPad|iPhone|iPod)/i);
    if(agentID)
        document.getElementById("documento").click();   
}

function registrarCambio(e)
{
    if(this.documento.files.length>0)
        this.label_archivo.innerHTML=this.documento.files.length.toString()+" archivo(s) seleccionado(s)";
    else
        this.label_archivo.innerHTML="Seleccione Archivo(s)";
}

function ordenarApellidos(a,b)
{
    s1=a.apellidoPaterno.toUpperCase();
    s2=b.apellidoPaterno.toUpperCase();
    if(s1<s2)
        return -1;
    else if (s1>s2)
        return 1;
    else
        return 0;
}

function cargarErrores(campo,negocio,sistema)
{
    var r=[];
    r=r.concat(campo);
    r=r.concat(negocio);
    r=r.concat(sistema);
    return r;
}

function cargarUsuarios(usuarios)
{
    return usuarios.sort(ordenarApellidos);
}

function cargarPendientes(usuarios)
{
    var r=[];
    i=0;
    while(i<usuarios.length)
    {
        if(usuarios[i].empresasSolicitadas && usuarios[i].empresasSolicitadas.length>0)
            r.push(usuarios[i]);
        i++;
    }
    return r.sort(ordenarApellidos);
}

var dec=angular.module('dec', ['ionic', 'dec.controllers'])
.run(function($rootScope,$ionicPlatform) {
  $ionicPlatform.ready(function() {
    // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
    // for form inputs)
    if (cordova.platformId === 'ios' && window.cordova && window.cordova.plugins.Keyboard) {
      cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
      cordova.plugins.Keyboard.disableScroll(true);

    }
    if (window.StatusBar) {
      // org.apache.cordova.statusbar required
      StatusBar.styleDefault();
    }
  });
})

.config(function($stateProvider, $urlRouterProvider) {
    $stateProvider

      .state('app', {
          url: '/app',
          abstract: true,
          templateUrl: 'templates/menu.html',
          controller: 'AppCtrl'
      })

    .state('app.home', {
        url: '/home',
        views: {
            'menuContent': {
                templateUrl: 'templates/home.html'
            }
        }
    })

   .state('app.me', {
        url: '/me',
        cache: false,
        views: {
            'menuContent': {
                templateUrl: 'templates/me.html',
                resolve: { authenticate: authenticate}
            }
        }
    })

   .state('app.registro', {
        url: '/registro',
        cache: false,
        views: {
            'menuContent': {
                templateUrl: 'templates/registro.html',
                controller: "RegistroCtrl"
            }
        }
    })

    .state('app.clave', {
        url: '/clave/:username',
        views: {
            'menuContent': {
                templateUrl: 'templates/clave.html',
                controller: 'ClaveCtrl'
           }
        }
    })

    .state('app.usuarios', {
        url: '/usuarios/:empresa',
        cache: false,
        views: {
            'menuContent': {
                templateUrl: 'templates/usuarios.html',
                controller: 'UsuariosCtrl',
                resolve: { authenticate: authenticate}
            }
        }
    })

    .state('app.usuario', {
        url: '/usuario/:rut',
        views: {
        'menuContent': {
            templateUrl: 'templates/usuario.html',
            controller: 'UsuarioCtrl',
            resolve: { authenticate: authenticate}
            }
        }
    })

    .state('app.empresas', {
        url: '/empresas/:rut',
        views: {
        'menuContent': {
            templateUrl: 'templates/empresas.html',
            controller: 'EmpresasCtrl',
            resolve: { authenticate: authenticate}
            }
        }
    })

    .state('app.accesos', {
        url: '/accesos/:rut',
        views: {
        'menuContent': {
            templateUrl: 'templates/accesos.html',
            controller: 'EmpresasCtrl',
            resolve: { authenticate: authenticate}
            }
        }
    })

    .state('app.seguridad', {
        url: '/seguridad/:empresa',
        cache: false,
        views: {
        'menuContent': {
            templateUrl: 'templates/seguridad.html',
            controller: 'SeguridadCtrl',
            resolve: { authenticate: authenticate}
            }
        }
    })

    .state('app.seguridad_roles', {
        url: '/seguridad/:empresa/:perfil',
        cache: false,
        views: {
        'menuContent': {
            templateUrl: 'templates/seguridad_roles.html',
            controller: 'SeguridadCtrl',
            resolve: { authenticate: authenticate}
            }
        }
    })

    .state('app.perfiles', {
        url: '/perfiles/:rut/:empresa/',
        views: {
        'menuContent': {
            templateUrl: 'templates/perfiles.html',
            controller: 'PerfilesCtrl',
            resolve: { authenticate: authenticate}
            }
        }
    })

     .state('app.documentos', {
         url: '/documentos/:empresa',
         cache: false,
         views: {
             'menuContent': {
                 templateUrl: 'templates/documentos.html',
                 controller: 'DocumentosCtrl',
                 resolve: { authenticate: authenticate}
             }
         }
     })

     .state('app.workflow', {
        url: '/workflow/:empresa',
        cache: false,
        views: {
            'menuContent': {
                templateUrl: 'templates/workflow.html',
                controller: 'WorkflowCtrl',
                resolve: { authenticate: authenticate}
            }
        }
    })
    .state('app.workflow_doc', {
        url: '/workflow/:empresa/:codigo_documento_workflow',
        cache: false,
        views: {
            'menuContent': {
                templateUrl: 'templates/workflow_doc.html',
                controller: 'WorkflowCtrl',
                resolve: { authenticate: authenticate}
            }
        }
    })
    .state('app.workflow_rol', {
        url: '/workflow/:empresa/:codigo_documento_workflow/:rol',
        cache: false,
        views: {
            'menuContent': {
                templateUrl: 'templates/workflow_rol.html',
                controller: 'WorkflowCtrl',
                resolve: { authenticate: authenticate}
            }
        }
    })
    .state('app.upload', {
         url: '/upload/:empresa',
         cache: false,
         views: {
             'menuContent': {
                 templateUrl: 'templates/upload.html',
                 controller: 'UploadCtrl',
                 resolve: { authenticate: authenticate}
             }
         }
     })

    .state('app.documento', {
        url: '/documento/:id',
        cache: false,
        views: {
            'menuContent': {
                templateUrl: 'templates/documento.html',
                controller: 'DocumentoCtrl',
                resolve: { authenticate: authenticate}
            }
        }
    });

    function authenticate($q, $rootScope, $state, $timeout)
    {
        if ($rootScope.loggedIn)
        {
            return $q.when()
        }
        else
        {
            $timeout(function() {
                $state.go('app.home')
            })
            return $q.reject()
        }
    }
    
    $urlRouterProvider.otherwise('/app/home');
});

function format_rut(campo)
{
    if(campo.indexOf(".")!=-1)
        return campo;
    campo=campo.replace(/^0+/, '');
    campo=campo.replace(/[-.]+/g, '');
    campo=campo.replace(/^[ ]+/, '');
    if(!campo || campo.length<1)
        return campo;
    i=campo.length-1;
    salida="-"+campo[i].toUpperCase();
    i=i-1;
    c=1;
    while(i>=0)
    {
        if(c%3==0)
        {
            salida="."+campo[i]+salida;
            c=1;
        }
        else
        {
            salida=campo[i]+salida;
            c++;
        }
        i--;
    }
    return salida;      
}

angular.module('dec.controllers', [])
.filter('zpad', function() {
    return function zpad(campo,ancho) {
        caracter="0";
        relleno=campo;
        while(relleno.length<ancho)
            relleno=caracter+relleno;
        return relleno;      
    }
})
.filter('spad', function() {
    return function spad(campo,ancho) {
        caracter=" ";
        relleno=campo;
        while(relleno.length<ancho)
            relleno=caracter+relleno;
        return relleno;      
    }
})
.filter('rutfmt', function() {
    return function rutfmt(campo) {
        return format_rut(campo);
    }
})
.directive('secure', function() {
  return {
    require: 'ngModel',
    link: function(scope, elm, attrs, ctrl) {
      ctrl.$validators.secure = function(value) {
        if(!value || value.length<6)
            return false;
        else
            return true;
      };
    }
  };
})
.directive('rutlen', function() {
  return {
    require: 'ngModel',
    link: function(scope, elm, attrs, ctrl) {
      ctrl.$validators.rutlen = function(value) {
        if(!value)
            return true;
        dv_ingresado=value.slice(-1);
        rut=value.slice(0,-1);
        rut=rut.replace(/^0+/, '');
        rut=rut.replace(/[-.]+/g, '');
        rut=rut.replace(/^[ ]+/, '');
        if(isNaN(rut) || rut.length<7)
            return false;
        return true;
      };
    }
  };
})
.directive('dvrut', function() {
  return {
    require: 'ngModel',
    link: function(scope, elm, attrs, ctrl) {
      ctrl.$validators.dvrut = function(value) {
        if(!value)
            return true;
        dv_ingresado=value.slice(-1).toUpperCase();
        rut=value.slice(0,-1);
        rut=rut.replace(/[-.]+/g, '');
        rut=rut.replace(/^[ ]+/, '');
        rut=rut.replace(/^0+/, '');       
        if(isNaN(rut))
            return false;
        var c=2;
        var d=0;
        for(i=rut.length-1;i>=0;i--)
        {
            d=d+parseInt(rut[i])*c;
            c=c+1;
            if(c>7)
                c=2;
        }
        resto=d%11;
        dv=11-resto;
        if(dv==10)
            dv="K";
        else if(dv==11)
            dv="0";
        else
            dv=dv.toString();
        if(dv!=dv_ingresado)
            return false;
        return true;
      };
    }
  };
})
.controller('RegistroCtrl', function($scope, $ionicHistory, $ionicPopup, $state, $location, $rootScope, $ionicModal, $timeout, $http) {
    $scope.usuario={"empresas":[]};
    $scope.empresa_seleccionada={"rut":""};
    $scope.errores=[];

    $scope.showAlert = function(mensaje) {
        var alertPopup = $ionicPopup.alert({
            title: 'DEC',
            template: "<div style='text-align: center'>"+mensaje+"</div>"
        });

        alertPopup.then(function(res) {
        });
    };

    $scope.delete = function(rut) {
        for(i=0;i<$scope.usuario.empresas.length;i++)
        {
            if($scope.usuario.empresas[i]==rut)
            {
                $scope.usuario.empresas.splice(i,1);
                return;
            }
        }
    };

    $scope.solicitarEmpresa = function() {
        rut_empresa=$scope.empresa_seleccionada.rut;
        if(rut_empresa=="")
            return;
        for(i=0;i<$scope.usuario.empresas.length;i++)
        {
            if($scope.usuario.empresas[i]==rut_empresa)
                return;
        }
        $scope.usuario.empresas.push(rut_empresa);
        $scope.empresa_seleccionada={"rut":""};
    };

    $scope.clavesDiferentes = function() {
        if($scope.usuario.clave != $scope.usuario.clave2)
            return true;
        else
            return false;
    };

    $scope.registrar = function() {
        mensaje = generarMensajeRegistrarUsuario("Registrarse",$scope.usuario);
        $http.post(servidor+"/apis/dec/usuarios/",mensaje,
            {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
                {
                    $scope.showAlert("Usuario registrado exitosamente");
                    $scope.usuario={"empresas":[]};
                    $scope.empresa_seleccionada={"rut":""};
                    $scope.errores=[];
                }
                else
                {
                    if(response.data.mensaje_dec.header.estado==5000)
                        $scope.showAlert("No tiene autorización para realizar la acción");
                    else
                        $scope.showAlert("Falló el registro del usuario");
                    header=response.data.mensaje_dec.header;
                    $scope.errores=cargarErrores(header.listaDeErroresCampo,header.listaDeErroresNegocio,header.listaDeErroresSistema);
                    $scope.empresa_seleccionada={"rut":""};
                }
            },
            function (response)
            {
                $scope.showAlert("Fallo la comunicación con el servicio");
                $scope.empresa_seleccionada={"rut":""};
            }
        );
    };
})
.controller('AppCtrl', function($scope, $ionicHistory, $ionicPopup, $state, $location, $rootScope, $ionicModal, $timeout, $http) {

    $scope.loginData = {};
    $rootScope.loggedIn=false;
    $rootScope.loginData=$scope.loginData;
    $scope.errores=[];

    $scope.format_rut = function(event) {
        event.target.value=format_rut(event.target.value);
    }

    $scope.unformat_rut = function(event) {
        event.target.value=event.target.value.replace(/\./g,"");
        event.target.value=event.target.value.replace(/\-/g,"");
    }

    $scope.showAlert = function(mensaje) {
        var alertPopup = $ionicPopup.alert({
            title: 'DEC',
            template: "<div style='text-align: center'>"+mensaje+"</div>"
        });

        alertPopup.then(function(res) {
        });
    };

    $scope.modificar = function () {
        mensaje = generarMensajeModificarUsuario("Actualizar datos",$scope.loginData.username, $scope.usuario);
        $http.post(servidor+"/apis/dec/usuarios/actualizar",mensaje,
        {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status==200 && response.data.mensaje_dec.header.estado == 0)
                {
                    $scope.showAlert("Datos modificados exitosamente");
                }
                else
                {
                    if(response.data.mensaje_dec.header.estado==5000)
                        $scope.showAlert("No tiene autorización para realizar la acción");
                    else
                        $scope.showAlert("Error en la modificación de datos");
                }
            },
            function (response) {
                    $scope.showAlert("Falló la comunicación con el servidor");
            });
    };

    $ionicModal.fromTemplateUrl('templates/login.html', {
        scope: $scope
    }).then(function(modal) {
        $scope.modal = modal;
    });

    $scope.closeLogin = function() {
        $scope.modal.hide();
    };

    $scope.login = function() {
        $scope.modal.show();
    };

    $scope.logout = function() {
        $scope.loginData={};
        $rootScope.loggedIn=false;
        $scope.usuario={};
        $rootScope.loginData=$scope.loginData;
        $ionicHistory.nextViewOptions({
            disableBack: true
        });
        $state.go('app.home', {}, {location: "replace", reload: true});
    };

    $scope.doLogin = function() {
        $scope.loginData.username=$scope.loginData.username.replace(/[-.]+/g, '').toUpperCase();
        mensaje = generarMensajeLogin("Ingresar",$scope.loginData.username, $scope.loginData.password);
        $http.post(servidor+"/apis/dec/usuarios/autenticacion",mensaje,
        {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                $scope.loginResponse = response.data;
                if ($scope.loginResponse.mensaje_dec.header.estado == 0)
                {
                    $rootScope.loggedIn = true;
                    $scope.errores=[];
                    $scope.closeLogin();
                    $scope.buscarUsuario();
                }
                else
                {
                    $rootScope.loggedIn = false;
                    header=response.data.mensaje_dec.header;
                    $scope.errores=cargarErrores(header.listaDeErroresCampo,header.listaDeErroresNegocio,header.listaDeErroresSistema);
                }
            },
            function (response) {
                $scope.errores.push({"errDescripcion":"Imposible comunicarse con el servidor"})
            });
    };

    $scope.buscarUsuario = function () {
        mensaje = generarMensajeConsultaUsuarios("Ingresar",$scope.loginData.username, $scope.loginData.username,"","","");
        $http.post(servidor+"/apis/dec/usuarios/datos/",mensaje,
            {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200)
                {
                    $scope.usuario = response.data.mensaje_dec.mensaje.ListaUsuario[0];
                    $scope.loginData.nombre=$scope.usuario.nombre.toUpperCase()+" "+$scope.usuario.apellidoPaterno.toUpperCase();
                    try
                    {
                        $scope.empresas= $scope.usuario.empresasAsignadas;
                        if($scope.empresas.length>0)
                        {
                            $scope.empresaSeleccionada=$scope.empresas[0].rutEmpresa;
                            $scope.razonSocialEmpresaSeleccionada=$scope.empresas[0].razonSocial;
                        }
                    } catch(error)
                    {
                        $scope.empresas=[];
                    }
                }
                else
                {
                    $scope.usuario={};
                    $scope.empresas=[];
                }
            },
            function (response)
            {
                $scope.usuario={};
                $scope.empresas=[];
            }
        );
    };

    $scope.cambiarEmpresa = function(nueva)
    {
        $scope.empresaSeleccionada=nueva;
        for(i=0;i<$scope.empresas.length;i++)
        {
            if($scope.empresas[i].rutEmpresa==nueva)
            {
                $scope.razonSocialEmpresaSeleccionada=$scope.empresas[i].razonSocial;
                break;
            }
        }
    }

    $scope.recuperarClave = function()
    {
        mensaje=generarMensajeOlvidoClave("Recuperar Clave",$scope.loginData.username);
        $http.post(servidor+"/apis/dec/usuarios/olvidoclave/",mensaje,
            {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
                {
                    $scope.showAlert("Se ha enviado un mensaje de recuperación de clave");
                }
                else
                {
                    $scope.showAlert("Falló la recuperación de clave");
                }
            },
            function (response)
            {
                $scope.showAlert("Falló la comunicación con el servidor");
            }
        );        
    };
})
.controller('UsuariosCtrl', function($scope, $rootScope, $http, $stateParams, $ionicLoading, $ionicPopup,$stateParams)
{
    $scope.rut_empresa=$stateParams.empresa;    
    $scope.buscar = function()
    {
        var rut=$scope.filtro.rut;
        var nombre=$scope.filtro.nombre;
        var apellidoPaterno=$scope.filtro.apellido;
        var estado=$scope.filtro.estado;
        $ionicLoading.show({
            template: '<ion-spinner></ion-spinner>'
        });
        mensaje = generarMensajeConsultaGeneralUsuarios("Usuarios",$rootScope.loginData.username, $scope.rut_empresa, rut,nombre,apellidoPaterno,estado);
        $http.post(servidor+"/apis/dec/usuarios/busqueda/",mensaje,
        {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
                {
                    try
                    {
                        $scope.usuarios = cargarUsuarios(response.data.mensaje_dec.mensaje.ListaUsuarios);
                    } catch(error)
                    {
                        $scope.usuarios=[];
                    }
                    try
                    {
                        $scope.pendientes = cargarPendientes(response.data.mensaje_dec.mensaje.ListaUsuarios);
                    } catch(error)
                    {
                        $scope.pendientes=[];
                    }
                    $ionicLoading.hide();
                }
                else
                {
                    $scope.usuarios=[];
                    $scope.pendientes=[];
                    $ionicLoading.hide();
                    header=response.data.mensaje_dec.header;
                    $scope.errores=cargarErrores(header.listaDeErroresCampo,header.listaDeErroresNegocio,header.listaDeErroresSistema);
                    if(response.data.mensaje_dec.header.estado==5000)
                        $ionicPopup.alert({title: "DEC",template: "No tiene autorización para realizar la acción"});
                    else
                        $ionicPopup.alert({title: "DEC",template: "Falló la búsqueda de usuarios"});
                }
            },
            function (response)
            {
                $scope.usuarios=[];
                $scope.pendientes=[];
                $ionicLoading.hide();
                $ionicPopup.alert({title: "DEC",template: "Falló la comunicación con el servidor"});
            }
        );
    };

    $scope.usuarios=[];
    $scope.pendientes=[];
    $scope.filtro = {"rut":"","nombre":"","apellido":"","estado":""};
    $scope.buscar();
})
.controller('UsuarioCtrl', function($scope, $rootScope, $ionicPopup, $ionicModal, $timeout, $http, $stateParams) {
    $scope.rut=$stateParams.rut;
    $scope.usuario={"rut":""};

    $scope.showAlert = function(mensaje) {
        var alertPopup = $ionicPopup.alert({
            title: 'DEC',
            template: "<div style='text-align: center'>"+mensaje+"</div>"
        });

        alertPopup.then(function(res) {
        });
    };

    mensaje = generarMensajeConsultaUsuarios("Mis Datos",$rootScope.loginData.username, $scope.rut,"","","");
    $http.post(servidor+"/apis/dec/usuarios/datos/",mensaje,
        {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
        function (response) {
            if (response.status == 200 && response.data.mensaje_dec.header.estado == 0)
            {
                try
                {
                    $scope.usuario = response.data.mensaje_dec.mensaje.ListaUsuario[0];
                } catch(error)
                {
                    $scope.showAlert("Falló la obtención del detalle del usuario");
                    $scope.usuario={"rut":""};
                }
            }
            else
            {
                if(response.data.mensaje_dec.header.estado==5000)
                    $scope.showAlert("No tiene autorización para realizar la acción");
                $scope.usuario={"rut":""};
            }
        },
        function (response)
        {
            $scope.showAlert("Falló la comunicación con el servicio");
            $scope.usuario={"rut":""};
        }
    );
})
.controller('EmpresasCtrl', function($scope, $rootScope, $ionicPopup, $ionicModal, $timeout, $http, $stateParams) {
    $scope.rut=$stateParams.rut;
    $scope.empresasSolicitadas=[];
    $scope.empresasAsignadas=[];
    $scope.empresasSolicitadasObjeto=[];
    $scope.empresasAsignadasObjeto=[];
    $scope.empresa_seleccionada={"rut":""};
    $scope.errores=[];

    $scope.clearStatus = function() {
        $scope.errores=[];
    }

    $scope.showAlert = function(mensaje) {
        var alertPopup = $ionicPopup.alert({
            title: 'DEC',
            template: "<div style='text-align: center'>"+mensaje+"</div>"
        });

        alertPopup.then(function(res) {
        });
    };

    $scope.actualizarSolicitudes = function(accion,empresa)
    {
        mensaje=generarMensajeSolicitaEmpresas("Consulta datos de Usuarios",$rootScope.username,$scope.rut,$scope.empresasSolicitadas);
        $http.post(servidor+"/apis/dec/usuarios/actualizar",mensaje,
        {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status==200 && response.data.mensaje_dec.header.estado == 0)
                {
                    $scope.empresa_seleccionada={"rut":""};
                    $scope.actualizarEmpresas();
                }
                else
                {
                    if(accion=="A")
                    {
                        $scope.empresasSolicitadas.pop();
                        $scope.showAlert("No se pudo solicitar la empresa");
                    }
                    else
                    {
                        $scope.empresasSolicitadas.push(empresa);
                        $scope.showAlert("No se pudo eliminar la empresa");
                    }
                    header=response.data.mensaje_dec.header;
                    $scope.errores=cargarErrores(header.listaDeErroresCampo,header.listaDeErroresNegocio,header.listaDeErroresSistema);
                }
            },
            function (response) {
                    $scope.showAlert("Falló la comunicación con el servidor");
        });
    }

    $scope.quitarEmpresa = function(empresa) {
        var i;
        $scope.errores=[];
        if(empresa=="")
            return;
        i=$scope.empresasSolicitadas.indexOf(empresa);
        if(i!=-1)
        {
            $scope.empresasSolicitadas.splice(i,1);
            $scope.actualizarSolicitudes("E",empresa);
        }
    };

    $scope.agregarEmpresa = function() {
        rut_empresa=$scope.empresa_seleccionada.rut;
        $scope.errores=[];
        if(rut_empresa=="")
            return;
        for(i=0;i<$scope.empresasSolicitadas.length;i++)
        {
            if($scope.empresasSolicitadas[i]==rut_empresa)
                return;
        }
        for(i=0;i<$scope.empresasAsignadas.length;i++)
        {
            if($scope.empresasAsignadas[i]==rut_empresa)
                return;
        }
        $scope.empresasSolicitadas.push(rut_empresa);
        $scope.actualizarSolicitudes("A",rut_empresa);
    };
                
    $scope.autorizar = function(empresa,accion) {
        mensaje = generarMensajeAutorizaEmpresa("Consulta datos de Usuarios",$rootScope.loginData.username, $scope.rut, empresa,accion);
        $http.post(servidor+"/apis/dec/usuarios/administraEmpresas/",mensaje,
            {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                var i;
                if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
                {
                    $scope.errores=[];
                    if(accion=="SI")
                    {
                        i=$scope.empresasSolicitadas.indexOf(empresa);
                        $scope.empresasSolicitadas.splice(i,1);
                        $scope.empresasAsignadas.push(empresa);
                    }
                    else
                    {
                        i=$scope.empresasSolicitadas.indexOf(empresa);
                        if(i!=-1)
                            $scope.empresasSolicitadas.splice(i,1);
                        else
                        {
                            i=$scope.empresasAsignadas.indexOf(empresa);
                            if(i!=-1)
                                $scope.empresasAsignadas.splice(i,1);
                        }
                    }
                    $scope.actualizarEmpresas();
                }
                else
                {
                    if(response.data.mensaje_dec.header.estado==5000)
                        $scope.showAlert("No tiene autorización para realizar la acción");
                    else
                        $scope.showAlert("No se pudo autorizar la empresa");
                    header=response.data.mensaje_dec.header;
                    $scope.errores=cargarErrores(header.listaDeErroresCampo,header.listaDeErroresNegocio,header.listaDeErroresSistema);
                }
            },
            function (response)
            {
                $scope.errores=[];
                $scope.showAlert("Fallo la comunicación con el servicio");
            }
        );
    };
    $scope.actualizarEmpresas = function()
    {
        mensaje = generarMensajeConsultaUsuarios("Consulta datos de Usuarios",$rootScope.loginData.username, $scope.rut,"","","");
        $http.post(servidor+"/apis/dec/usuarios/datos/",mensaje,
            {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
                {
                    usuario = response.data.mensaje_dec.mensaje.ListaUsuario[0];
                    $scope.empresasAsignadasObjeto=usuario.empresasAsignadas;
                    $scope.empresasSolicitadasObjeto=usuario.empresasSolicitadas;
                    $scope.empresasAsignadas=[];
                    for(i=0;i<$scope.empresasAsignadasObjeto.length;i++)
                            $scope.empresasAsignadas.push($scope.empresasAsignadasObjeto[i].rutEmpresa);
                    $scope.empresasSolicitadas=[];
                    for(i=0;i<$scope.empresasSolicitadasObjeto.length;i++)
                            $scope.empresasSolicitadas.push($scope.empresasSolicitadasObjeto[i].rutEmpresa);
                }
                else
                {
                    if(response.data.mensaje_dec.header.estado==5000)
                        $scope.showAlert("No tiene autorización para realizar la acción");
                    $scope.empresasSolicitadasObjeto=[];
                    $scope.empresasAsignadasObjeto=[];
                }
            },
            function (response)
            {
                $scope.empresasSolicitadasObjeto=[];
                $scope.empresasAsignadasObjeto=[];
            }
        );
    }
    $scope.actualizarEmpresas();
})
.controller('PerfilesCtrl', function($scope, $rootScope, $ionicModal, $timeout, $http, $stateParams) {
    $scope.rut=$stateParams.rut;
    $scope.empresa=$stateParams.empresa;
    $scope.perfiles=[];
    $scope.perfiles_usuario=[];
    $scope.errores=[];

    $scope.autorizar = function(perfil,nombre,accion)
    {
        $scope.errores=[];
        if(accion=="A")
            $scope.agregarPerfil(perfil,nombre);
        else
            $scope.quitarPerfil(perfil);
        mensaje = generarMensajeAutorizaPerfil("Otorgar Perfiles",$rootScope.loginData.username, $scope.rut, $scope.empresa, $scope.perfiles_usuario);
        $http.post(servidor+"/apis/dec/usuarios/autorizar/",mensaje,
            {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
                {
                    $scope.errores=[];
                }
                else
                {
                    if(response.data.mensaje_dec.header.estado==5000)
                        $scope.showAlert("No tiene autorización para realizar la acción");
                    else
                        $scope.showAlert("No se pudo agregar el perfil");
                    if(accion=="A")
                        $scope.quitarPerfil(perfil);
                    else
                        $scope.agregarPerfil(perfil,nombre);
                    header=response.data.mensaje_dec.header;
                    $scope.errores=cargarErrores(header.listaDeErroresCampo,header.listaDeErroresNegocio,header.listaDeErroresSistema);
                }
            },
            function (response)
            {
                $scope.errores=[];
                if(accion=="A")
                    $scope.quitarPerfil(perfil);
                else
                    $scope.agregarPerfil(perfil,nombre);
                $scope.showAlert("Fallo la comunicación con el servicio");
            }
        );
    };

    $scope.agregarPerfil = function(codigo,nombre) {
        if(!nombre || nombre.length==0)
            return;
        for(i=0;i<$scope.perfiles_usuario.length;i++)
        {
            if($scope.perfiles_usuario[i].id==codigo)
            {
                return;
            }
        }
        for(i=0;i<$scope.perfiles.length;i++)
        {
            if($scope.perfiles[i].id==codigo)
            {
                $scope.perfiles.splice(i,1);
                break;
            }
        }
        perfil={"id":codigo, "nombrePerfil":nombre};
        $scope.perfiles_usuario.push(perfil);
    };

    $scope.quitarPerfil = function(codigo) {
        if(!codigo || codigo.length==0)
            return;
        for(i=0;i<$scope.perfiles_usuario.length;i++)
        {
            if($scope.perfiles_usuario[i].id==codigo)
            {
                $scope.perfiles.push({"id":codigo,"nombrePerfil":$scope.perfiles_usuario[i].nombrePerfil});
                $scope.perfiles_usuario.splice(i,1);
                return;
            }
        }
    };

    $scope.buscarPerfilesUsuario = function(perfiles) {
        mensaje = generarMensajeConsultaUsuarios("Consulta datos de Usuarios",$rootScope.loginData.username, $scope.rut,"","","");
        $http.post(servidor+"/apis/dec/usuarios/datos/",mensaje,
            {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
                {
                    $scope.usuario = response.data.mensaje_dec.mensaje.ListaUsuario[0];
                    empresas=$scope.usuario.empresasAsignadas;
                    if(!empresas)
                        empresas=[];
                    $scope.perfiles_usuario = [];
                    for(i=0;i<empresas.length;i++)
                    {
                        if(empresas[i].rutEmpresa.toUpperCase()==$scope.empresa.toUpperCase())
                        {
                            for(j=0;j<empresas[i].perfiles.length;j++)
                            {
                                codigoPerfil=empresas[i].perfiles[j].id;
                                perfil=$scope.traductorPerfiles[codigoPerfil];
                                index=perfiles.indexOf(perfil);
                                perfiles.splice(index,1);
                                $scope.perfiles_usuario.push(perfil);
                            }
                        }
                    }
                    $scope.perfiles=perfiles;
                }
                else
                {
                    if(response.data.mensaje_dec.header.estado==5000)
                        $scope.showAlert("No tiene autorización para realizar la acción");
                    $scope.perfiles_usuario = [];
                    $scope.perfiles=perfiles;
                }
            },
            function (response)
            {
                $scope.perfiles_usuario = [];
                $scope.perfiles=perfiles;
            }
        );
    }

    mensaje = generarMensajeConsultaPerfiles("Consulta datos de Usuarios",$rootScope.loginData.username, $scope.empresa);
    $http.post(servidor+"/apis/dec/clientes/perfiles/",mensaje,
        {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
        function (response) {
            if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
            {
                perfiles=response.data["mensaje_dec"]["mensaje"]["Lista Perfiles"];
                $scope.traductorPerfiles = {};
                for(i=0;i<perfiles.length;i++)
                {
                    $scope.traductorPerfiles[perfiles[i].id]=perfiles[i];
                }
                $scope.buscarPerfilesUsuario(perfiles);
            }
            else
            {
                if(response.data.mensaje_dec.header.estado==5000)
                    $scope.showAlert("No tiene autorización para realizar la acción");
                $scope.perfiles = [];
            }
        },
        function (response)
        {
            $scope.perfiles = [];
        }
    );
})
.controller('DocumentoCtrl', function($scope, $rootScope, $ionicModal, $timeout, $http, $stateParams,DocumentosPendientes) {
    $scope.id=$stateParams.id;
    $scope.firma={};
    $scope.firma.rut_firmante="";
    $scope.firma.nombre_firmante="";
    $scope.documento=DocumentosPendientes.getDocumento($scope.id);
    $scope.usuario=$rootScope.loginData.username;

    $scope.copiarFirmantes = function(origen,destino)
    {
        var i;
        for(i=0;i<origen.firmantes.length;i++)
            destino.firmantes[i].usuarios=origen.firmantes[i].usuarios;
    }

    $scope.unformat_rut = function(s) {
        if(s && s.length>0)
        {
            s=s.replace(/\./g,"");
            s=s.replace(/\-/g,"");
        }
        return s;
    }

    $scope.firmar = function(nombrePerfil,descripcionPerfil)
    {
        etiqueta="";
        if(descripcionPerfil!="PERSONAL")
        {
            rut=$rootScope.loginData.username;
            nombre=$rootScope.loginData.nombre;
            etiqueta="Firma Usuario";
        }
        else
        {
            if($scope.firma.rut_firmante=="" || $scope.firma.nombre_firmante=="")
            {
                $scope.showAlert("Debe ingresar los datos del tercero que firma.");
                return;
            }
            rut=$scope.unformat_rut($scope.firma.rut_firmante);
            nombre=$scope.firma.nombre_firmante.toUpperCase();
            etiqueta="Firma un Tercero";
        }
        mensaje=generarMensajeFirma(etiqueta,$rootScope.loginData.username,$scope.empresaSeleccionada,
        $scope.documento.idAcepta, "7650-KQ54-0973-H630", rut, nombre, nombrePerfil, descripcionPerfil);
        $http.post(servidor+"/apis/dec/firmantes/firmar",mensaje,
        {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
                {
                    $scope.showAlert("Documento firmado exitosamente");
                    $scope.copiarFirmantes($scope.documento,response.data.mensaje_dec.mensaje);
                    $scope.documento=response.data.mensaje_dec.mensaje;
                    docfirma=document.getElementById('docfirma');
                    docfirma.src=$scope.documento.url;
                }
                else
                {
                    if(response.data.mensaje_dec.header.estado==5000)
                        $scope.showAlert("No tiene autorización para realizar la acción");
                    else
                        $scope.showAlert("Falló la firma del documento");
                    header=response.data.mensaje_dec.header;
                    $scope.errores=cargarErrores(header.listaDeErroresCampo,header.listaDeErroresNegocio,header.listaDeErroresSistema);
                }
            },
            function (response)
            {
                $scope.showAlert("Falló la comunicación con el servidor");
            }
        );  
    };
})
.controller('DocumentosCtrl', function($scope, $rootScope, $ionicModal, $timeout, $http, $stateParams, $ionicLoading, DocumentosPendientes) {
    $scope.empresa=$stateParams.empresa;
    $scope.filtro={estado: "PENDIENTE FIRMA"};
    $scope.changeTipo = function(nuevo)
    {
        for(i=0;i<$scope.tipos_documento.length;i++)
        {
            if($scope.tipos_documento[i].codigo == nuevo)
            {
                $scope.tipo_documento_seleccionado=$scope.tipos_documento[i].codigo;
                $scope.subtipos_documento=$scope.tipo_documento_seleccionado.subtipoDocumentos;
                if($scope.subtipos_documento.length>0)
                {
                    $scope.subtipo_documento_seleccionado=$scope.subtipos_documento[0].codigo;
                }
                break;
            }                
        }
    }
    $scope.changeSubtipo = function(nuevo)
    {
        $scope.subtipo_documento_seleccionado=nuevo;
    }
    $scope.buscarTipos = function()
    {
        if(!$scope.empresa)
        {
            $scope.tipos_documento=[];
            $scope.showAlert("El usuario no tiene una empresa asociada");
            return;            
        }
        mensaje = generarMensajeConsultaTipoDocumentos("Firma Documentos",$rootScope.loginData.username,$scope.empresa);
        $http.post(servidor+"/apis/dec/TipoDocumentos/busqueda",mensaje,
        {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
                {
                    $scope.tipos_documento = response.data.mensaje_dec.mensaje;
                    if($scope.tipos_documento.length>0)
                    {
                        $scope.tipo_documento_seleccionado=$scope.tipos_documento[0].codigo;
                        $scope.subtipos_documento=$scope.tipos_documento[0].subtipoDocumentos;
                        if($scope.subtipos_documento.length>0)
                        {
                            $scope.subtipo_documento_seleccionado=$scope.subtipos_documento[0].codigo;
                            $scope.buscar();
                        }
                    }                           
                }
                else
                {
                    $scope.tipos_documento=[];
                    if(response.data.mensaje_dec.header.estado==5000)
                        $scope.showAlert("No tiene autorización para realizar la acción");
                    else
                        $scope.showAlert("Falló la busqueda de tipos de documento");
                    header=response.data.mensaje_dec.header;
                    $scope.errores=cargarErrores(header.listaDeErroresCampo,header.listaDeErroresNegocio,header.listaDeErroresSistema);
                }
            },
            function (response)
            {
                $scope.tipos_documento=[];
            }
        );
    }
    $scope.buscar = function()
    {
        $ionicLoading.show({
            template: '<ion-spinner></ion-spinner>'
        });
        if(!$scope.empresa)
        {
            $scope.tipos_documento=[];
            $scope.showAlert("El usuario no tiene una empresa asociada");
            return;            
        }
        mensaje = generarMensajeConsultaDocumentos("Firma Documentos",$rootScope.loginData.username,
                  $scope.empresa, $scope.tipo_documento_seleccionado,$scope.subtipo_documento_seleccionado,$scope.filtro);
        $http.post(servidor+"/apis/dec/OperaDocumentos/busqueda",mensaje,
        {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
                {
                    $scope.documentos = response.data.mensaje_dec.mensaje;
                    DocumentosPendientes.setDocumentosPendientes($scope.documentos);
                    $ionicLoading.hide();
                }
                else
                {
                    $scope.documentos=[];
                    $ionicLoading.hide();
                    if(response.data.mensaje_dec.header.estado==5000)
                        $scope.showAlert("No tiene autorización para realizar la acción");
                    else
                        $scope.showAlert("Falló la busqueda de documentos");
                    header=response.data.mensaje_dec.header;
                    $scope.errores=cargarErrores(header.listaDeErroresCampo,header.listaDeErroresNegocio,header.listaDeErroresSistema);
                }
            },
            function (response)
            {
                $ionicLoading.hide();
                $scope.showAlert("Falló la comunicación con el servidor");
                $scope.documentos=[];
            }
        );
    };
    $scope.buscarTipos();
})
.controller('UploadCtrl', function($scope, $rootScope, $ionicModal, $timeout, $http, $stateParams, $ionicLoading, DocumentosPendientes) {
    $scope.empresa=$stateParams.empresa;

    $scope.inicializar = function()
    {
        $scope.progreso=0;
        $scope.incremento=0;
    }
    $scope.upload = function(descripcion)
    {
        $scope.progreso=0;
        $ionicLoading.show({
            template: '<ion-spinner></ion-spinner>'
        });
        var r = new FileReader();
        function read(indice)
        {
            funcion_cargadora = function(e)
            {
                var data = e.target.result;
                var f = elemento.files[indice];
                $scope.incremento=100/elemento.files.length;
                contenido=data.split(",");
                if(contenido.length!=2)
                {
                    $ionicLoading.hide();
                    $scope.showAlert("Archivo de formato incorrecto");
                    return;
                }
                mensaje=generarMensajeUpload("Cargar documentos",$rootScope.loginData.username, $scope.empresa,
                $scope.tipo_documento_seleccionado, $scope.subtipo_documento_seleccionado, f.name, descripcion, contenido[1], f.size);
                $http.post(servidor+"/apis/dec/OperaDocumentos/carga",mensaje,
                {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
                    function (response) {
                        if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
                        {
                            $scope.progreso=$scope.progreso+$scope.incremento;
                            read(indice+1);
                        }
                        else
                        {
                            $ionicLoading.hide();
                            $scope.showAlert("Falló la carga de documentos");
                        }
                    },
                    function (response)
                    {
                        $ionicLoading.hide();
                        if(response.data.mensaje_dec.header.estado==5000)
                            $scope.showAlert("No tiene autorización para realizar la acción");
                        else
                            $scope.showAlert("Falló la carga de documentos");
                    }
                );
            }
            elemento=document.getElementById('documento');
            if(indice>=elemento.files.length)
            {
                $ionicLoading.hide();
                $scope.showAlert("Carga Finalizada");
                document.getElementById('nombre_documento').value=null;
                document.getElementById('documento').value=null;
                return;
            }
            else
            {
                r.onloadend=funcion_cargadora;
                var f = elemento.files[indice];
                r.readAsDataURL(f);
            }
        }
        read(0);
    }
    $scope.changeTipo = function(nuevo)
    {
        for(i=0;i<$scope.tipos_documento.length;i++)
        {
            if($scope.tipos_documento[i].codigo == nuevo)
            {
                $scope.tipo_documento_seleccionado=$scope.tipos_documento[i].codigo;
                $scope.subtipos_documento=$scope.tipo_documento_seleccionado.subtipoDocumentos;
                if($scope.subtipos_documento.length>0)
                {
                    $scope.subtipo_documento_seleccionado=$scope.subtipos_documento[0].codigo;
                }
                break;
            }                
        }
    }
    $scope.changeSubtipo = function(nuevo)
    {
        $scope.subtipo_documento_seleccionado=nuevo;
    }
    $scope.buscarTipos = function()
    {
        if(!$scope.empresa)
        {
            $scope.tipos_documento=[];
            $scope.showAlert("El usuario no tiene una empresa asociada");
            return;            
        }
        mensaje = generarMensajeConsultaTipoDocumentos("Cargar documentos",$rootScope.loginData.username,$scope.empresa);
        $http.post(servidor+"/apis/dec/TipoDocumentos/busqueda",mensaje,
        {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
                {
                    $scope.tipos_documento = response.data.mensaje_dec.mensaje;
                    if($scope.tipos_documento.length>0)
                    {
                        $scope.tipo_documento_seleccionado=$scope.tipos_documento[0].codigo;
                        $scope.subtipos_documento=$scope.tipos_documento[0].subtipoDocumentos;
                        if($scope.subtipos_documento.length>0)
                        {
                            $scope.subtipo_documento_seleccionado=$scope.subtipos_documento[0].codigo;
                        }
                    }                           
                }
                else
                {
                    $scope.tipos_documento=[];
                    if(response.data.mensaje_dec.header.estado==5000)
                        $scope.showAlert("No tiene autorización para realizar la acción");
                    else
                        $scope.showAlert("Falló la busqueda de tipos de documento");
                    header=response.data.mensaje_dec.header;
                    $scope.errores=cargarErrores(header.listaDeErroresCampo,header.listaDeErroresNegocio,header.listaDeErroresSistema);
                }
            },
            function (response)
            {
                $scope.tipos_documento=[];
            }
        );
    }
    $scope.buscarTipos();
    $scope.inicializar();
})
.controller('ClaveCtrl', function($scope, $rootScope, $ionicPopup, $ionicModal, $timeout, $http, $stateParams) {
    $scope.showAlert = function(mensaje) {
        var alertPopup = $ionicPopup.alert({
            title: 'DEC',
            template: "<div style='text-align: center'>"+mensaje+"</div>"
        });

        alertPopup.then(function(res) {
        });
    };

    $scope.cuenta = { "username": $stateParams.username,  "clave": "", "clave2": ""};
    $scope.clavesDiferentes = function() {
        if($scope.cuenta.clave != $scope.cuenta.clave2)
            return true;
        else
            return false;
    };
    $scope.cambiarClave = function()
    {
        mensaje=generarMensajeCambioClave("Cambiar Clave",$scope.cuenta.username,$scope.cuenta.clave);
        $http.post(servidor+"/apis/dec/usuarios/cambioclave/",mensaje,
            {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
                {
                    $scope.showAlert("Cambio de clave exitoso");
                    $scope.cuenta = { "username": $stateParams.username,  "clave": "", "clave2": ""};
                }
                else
                {
                    if(response.data.mensaje_dec.header.estado==5000)
                        $scope.showAlert("No tiene autorización para realizar la acción");
                    else
                        $scope.showAlert("Fallo el cambio de clave");
                }
            },
            function (response)
            {
                $scope.showAlert("Falló la comunicación con el servidor");
            }
        );        
    };
})
.controller('WorkflowCtrl', function($scope, $rootScope, $ionicModal, $timeout, $http, $stateParams, $ionicLoading, DocumentosWorkflow) {
    $scope.empresa=$stateParams.empresa;
    $scope.codigo_documento_workflow=$stateParams.codigo_documento_workflow;
    $scope.rol=$stateParams.rol;
    $scope.documento_workflow={};
    $scope.rol_workflow={};
    $scope.nuevo_documento={};
    $scope.nuevo_firmante={};
    $scope.creacion_habilitada=false;
    $scope.borrado_habilitado=false;
    $scope.ordenacion_habilitada=false;
    $scope.perfiles=[];
    $scope.habilitarCreacion=function()
    {
        if(!$scope.borrado_habilitado)
            $scope.creacion_habilitada=!$scope.creacion_habilitada;
    }
    $scope.habilitarOrdenacion=function()
    {
        if(!$scope.borrado_habilitado && !$scope.creacion_habilitada)
            $scope.ordenacion_habilitada=!$scope.ordenacion_habilitada;
    }
    $scope.habilitarBorrado=function()
    {
        if(!$scope.creacion_habilitada)
            $scope.borrado_habilitado=!$scope.borrado_habilitado;
    }
    $scope.crearWorkflow = function()
    {
        $scope.nuevo_documento.tipoDocumento=$scope.tipo_documento_seleccionado;
        $scope.nuevo_documento.firmantes=[];
        $scope.crearSubtipoDocumento();
    }
    $scope.borrarWorkflow = function(item)
    {
        $scope.subtipos_documento.splice($scope.subtipos_documento.indexOf(item), 1);
        $scope.borrado_habilitado=false;
    }
    $scope.agregarFirmante = function(codigo,nombre)
    {
        $scope.nuevo_firmante["orden"]=$scope.documento_workflow.firmantes.length+1
        $scope.nuevo_firmante.nombrePerfil=codigo;
        $scope.nuevo_firmante.descripcionPerfil=nombre;
        $scope.documento_workflow.firmantes.push($scope.nuevo_firmante);
        $scope.actualizarSubtipoDocumento("agregarFirmante");
    }
    $scope.moverFirmante = function(item, fromIndex, toIndex)
    {
        $scope.documento_workflow.firmantes.splice(fromIndex, 1);
        $scope.documento_workflow.firmantes.splice(toIndex, 0, item);
        for(i=0;i<$scope.documento_workflow.firmantes.length;i++)
        {
            orden=i+1;
            $scope.documento_workflow.firmantes[i].orden=orden.toString();
        }
        $scope.actualizarSubtipoDocumento("moverFirmante");
    };
    $scope.borrarFirmante = function(item)
    {
        $scope.documento_workflow.firmantes.splice($scope.documento_workflow.firmantes.indexOf(item), 1);
        for(i=0;i<$scope.documento_workflow.firmantes.length;i++)
        {
            orden=i+1;
            $scope.documento_workflow.firmantes[i].orden=orden.toString();
        }
        $scope.borrado_habilitado=false;
        $scope.actualizarSubtipoDocumento("borrarFirmante");
    }
    $scope.changeTipo = function(nuevo)
    {
        for(i=0;i<$scope.tipos_documento.length;i++)
        {
            if($scope.tipos_documento[i].codigo == nuevo)
            {
                $scope.tipo_documento_seleccionado=$scope.tipos_documento[i].codigo;
                $scope.subtipos_documento=$scope.tipo_documento_seleccionado.subtipoDocumentos;
                if($scope.subtipos_documento.length>0)
                {
                    $scope.subtipo_documento_seleccionado=$scope.subtipos_documento[0].codigo;
                }
                break;
            }                
        }
    }
    $scope.buscarTipos = function()
    {
        if(!$scope.empresa)
        {
            $scope.tipos_documento=[];
            $scope.showAlert("El usuario no tiene una empresa asociada");
            return;            
        }
        mensaje = generarMensajeConsultaTipoDocumentos("Firma Documentos",$rootScope.loginData.username,$scope.empresa);
        $http.post(servidor+"/apis/dec/TipoDocumentos/busqueda",mensaje,
        {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
                {
                    $scope.tipos_documento = response.data.mensaje_dec.mensaje;
                    if($scope.tipos_documento.length>0)
                    {
                        $scope.tipo_documento_seleccionado=$scope.tipos_documento[0].codigo;
                        $scope.subtipos_documento=$scope.tipos_documento[0].subtipoDocumentos;
                        DocumentosWorkflow.setDocumentosWorkflow($scope.tipos_documento);
                    }                           
                }
                else
                {
                    $scope.tipos_documento=[];
                    if(response.data.mensaje_dec.header.estado==5000)
                        $scope.showAlert("No tiene autorización para realizar la acción");
                    else
                        $scope.showAlert("Falló la busqueda de tipos de documento");
                    header=response.data.mensaje_dec.header;
                    $scope.errores=cargarErrores(header.listaDeErroresCampo,header.listaDeErroresNegocio,header.listaDeErroresSistema);
                }
            },
            function (response)
            {
                $scope.tipos_documento=[];
            }
        );
    }
    $scope.crearSubtipoDocumento = function()
    {
        mensaje = generarMensajeCrearSubtipoDocumentos("Crear SubtipoDocumentos",$rootScope.loginData.username,$scope.empresa,$scope.nuevo_documento);
        $http.post(servidor+"/apis/dec/subtipodocumentos/crear",mensaje,
        {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200 && response.data.mensaje_dec.header.estado!=1)
                {
                    $scope.subtipos_documento.push($scope.nuevo_documento);
                    DocumentosWorkflow.addDocumentoWorkflow($scope.nuevo_documento);
                    $scope.creacion_habilitada=false;
                    $scope.nuevo_documento={};
                }
                else
                {
                    if(response.data.mensaje_dec.header.estado==5000)
                        $scope.showAlert("No tiene autorización para realizar la acción");
                    else
                        $scope.showAlert("Falló la creación del documento");
                    $scope.creacion_habilitada=false;
                    $scope.nuevo_documento={};
                }
            },
            function (response)
            {
                $scope.showAlert("Falló la creación del documento");
                $scope.creacion_habilitada=false;
                $scope.nuevo_documento={};
            }
        );
    }
    $scope.actualizarSubtipoDocumento = function(opcion)
    {
        mensaje = generarMensajeCrearSubtipoDocumentos("Actualizar SubtipoDocumentos",$rootScope.loginData.username,$scope.empresa,$scope.documento_workflow);
        $http.post(servidor+"/apis/dec/subtipodocumentos/actualizar",mensaje,
        {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200 && response.data.mensaje_dec.header.estado!=1)
                {
                    $scope.creacion_habilitada=false;
                    $scope.nuevo_firmante={};
                }
                else
                {
                    if(response.data.mensaje_dec.header.estado==5000)
                        $scope.showAlert("No tiene autorización para realizar la acción");
                    else
                        $scope.showAlert("Falló la actualización del documento");
                    $scope.creacion_habilitada=false;
                    $scope.nuevo_firmante={};
                }
            },
            function (response)
            {
                $scope.showAlert("Falló la actualización del documento");
                $scope.creacion_habilitada=false;
                $scope.nuevo_firmante={};
            }
        );
    }
    $scope.buscarFirmantes = function()
    {
        mensaje = generarMensajeConsultaPerfiles("Consulta Perfiles",$rootScope.loginData.username, $scope.empresa);
        $http.post(servidor+"/apis/dec/clientes/perfiles",mensaje,
            {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
                {
                    perfiles=response.data["mensaje_dec"]["mensaje"]["Lista Perfiles"];
                    $scope.perfiles=[];
                    for(i=0;i<perfiles.length;i++)
                    {
                        for(j=0;j<perfiles[i].roles.length;j++)
                        {
                            if(perfiles[i].roles[j]=="FIRMANTE")
                            {
                                $scope.perfiles.push(perfiles[i]);
                                break;                                
                            }
                        }
                    }
                }
                else
                {
                    if(response.data.mensaje_dec.header.estado==5000)
                        $scope.showAlert("No tiene autorización para realizar la acción");
                    $scope.perfiles = [];
                }
            },
            function (response)
            {
                $scope.perfiles = [];
            }
        );
    }
    if($scope.codigo_documento_workflow!=null)
    {
        $scope.documento_workflow=DocumentosWorkflow.getWorkflow($scope.codigo_documento_workflow);
        $scope.buscarFirmantes();
        if($scope.rol!=null)
        {
            for(i=0;i<$scope.documento_workflow.firmantes.length;i++)
            {
                if($scope.documento_workflow.firmantes[i].orden==$scope.rol)
                    $scope.rol_workflow=$scope.documento_workflow.firmantes[i];
            }
        }        
    }
    else
        $scope.buscarTipos();
})
.controller('SeguridadCtrl', function($scope, $rootScope, $ionicModal, $timeout, $http, $stateParams, PerfilFactory) {
    $scope.empresa=$stateParams.empresa;
    $scope.nombrePerfil=$stateParams.perfil;
    $scope.perfil={}
    $scope.perfiles=[];
    $scope.roles_posibles=[];
    $scope.borrado_habilitado=false;
    $scope.creacion_habilitada=false;
    $scope.nuevo_perfil={};
    $scope.nuevo_rol={};

    $scope.borrarPerfil = function(item)
    {
        $scope.perfiles.splice($scope.perfiles.indexOf(item), 1);
        $scope.borrado_habilitado=false;
    }
    $scope.habilitarCreacion=function()
    {
        if(!$scope.borrado_habilitado)
            $scope.creacion_habilitada=!$scope.creacion_habilitada;
    }
    $scope.habilitarBorrado=function()
    {
        if(!$scope.creacion_habilitada)
            $scope.borrado_habilitado=!$scope.borrado_habilitado;
    }
    $scope.crearPerfil = function()
    {
        $scope.nuevo_perfil["roles"]=[];
        $scope.nuevo_perfil["roles"].push($scope.nuevo_perfil["rol"]);
        mensaje=generarMensajeIngresaPerfil("Ingresa Perfil",$scope.usuario,$scope.empresa,$scope.nuevo_perfil);
        $http.post(servidor+"/apis/dec/perfiles/crear",mensaje,
        {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
        function (response) {
            if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
            {
                $scope.perfiles.push($scope.nuevo_perfil);
                $scope.creacion_habilitada=false;
                $scope.nuevo_perfil={};
            }
            else
            {
                if(response.data.mensaje_dec.header.estado==5000)
                    $scope.showAlert("No tiene autorización para realizar la acción");
                else
                    $scope.showAlert("Falló la creación del perfil");
                $scope.creacion_habilitada=false;
                $scope.nuevo_perfil={};
            }
        },
        function (response)
        {
            $scope.showAlert("Falló la creación del perfil");
            $scope.creacion_habilitada=false;
            $scope.nuevo_perfil={};
        }
        );
    }

    $scope.actualizarPerfil = function(rol)
    {
        if(rol == null)
            $scope.perfil.roles.push($scope.nuevo_rol.nombreRol);
        else
            $scope.perfil.roles.splice($scope.perfil.roles.indexOf(rol), 1);
        mensaje=generarMensajeIngresaPerfil("Actualiza Perfil",$scope.usuario,$scope.empresa,$scope.perfil);
        $http.post(servidor+"/apis/dec/perfiles/actualizar",mensaje,
        {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
        function (response) {
            if (response.status == 200 && response.data.mensaje_dec.header.estado!=1)
            {
                $scope.creacion_habilitada=false;
                $scope.borrado_habilitado=false;
                $scope.nuevo_rol={};
            }
            else
            {
                if(rol == null)
                    $scope.perfil.roles.splice($scope.perfil.roles.indexOf($scope.nuevo_rol.nombreRol),1);
                else
                    $scope.perfil.roles.push(rol);
                if(response.data.mensaje_dec.header.estado==5000)
                    $scope.showAlert("No tiene autorización para realizar la acción");
                else
                    $scope.showAlert("Falló la actualización de roles");
                $scope.creacion_habilitada=false;
                $scope.borrado_habilitado=false;
                $scope.nuevo_rol={};
            }
        },
        function (response)
        {
            $scope.showAlert("Falló la actualización de roles");
            $scope.creacion_habilitada=false;
            $scope.borrado_habilitado=false;
            $scope.nuevo_rol={};
        }
        );
    }

    $scope.agregarRol = function()
    {
        if($scope.perfil.roles.indexOf($scope.nuevo_rol.nombreRol)!=-1)
        {
            $scope.showAlert("No se puede agregar dos veces el mismo rol");
            $scope.creacion_habilitada=false;
            $scope.borrado_habilitado=false;
            $scope.nuevo_rol={};
        }
        else
            $scope.actualizarPerfil(null);
    }

    $scope.quitarRol = function(item)
    {
        $scope.actualizarPerfil(item);
    }

    $scope.buscar = function()
    {
        mensaje = generarMensajeConsultaPerfiles("Consulta Perfiles",$rootScope.loginData.username, $scope.empresa);
        $http.post(servidor+"/apis/dec/clientes/perfiles",mensaje,
            {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
                {
                    perfiles=response.data["mensaje_dec"]["mensaje"]["Lista Perfiles"];
                    $scope.perfiles=perfiles;
                    PerfilFactory.setPerfiles(perfiles);
                }
                else
                {
                    if(response.data.mensaje_dec.header.estado==5000)
                        $scope.showAlert("No tiene autorización para realizar la acción");
                    $scope.perfiles = [];
                }
            },
            function (response)
            {
                $scope.perfiles = [];
            }
        );
    }

    $scope.buscarRoles = function()
    {
        mensaje = generarMensajeConsultaRoles("Consulta Roles",$rootScope.loginData.username, $scope.empresa);
        $http.post(servidor+"/apis/dec/roles/busqueda",mensaje,
            {headers: {"Content-type": "application/x-www-form-urlencoded"}}).then(
            function (response) {
                if (response.status == 200 && response.data.mensaje_dec.header.estado==0)
                {
                    $scope.roles_posibles=response.data["mensaje_dec"]["mensaje"]["Lista Roles"];
                }
                else
                {
                    if(response.data.mensaje_dec.header.estado==5000)
                        $scope.showAlert("No tiene autorización para realizar la acción");
                    $scope.roles_posibles = [];
                }
            },
            function (response)
            {
                $scope.roles_posibles = [];
            }
        );
    }

    if($scope.nombrePerfil==null)
        $scope.buscar();
    else
    {
        $scope.perfil=PerfilFactory.getPerfil($scope.nombrePerfil);
    }
    $scope.buscarRoles();
});

dec.factory('PerfilFactory', function() {
    var perfiles=[];

    return {
        setPerfiles : function(d) {
            perfiles=d;
        },
        getPerfil : function(id) {
            for(i=0;i<perfiles.length;i++)
            {
                if(perfiles[i].nombrePerfil==id)
                    return perfiles[i];
            }
            return null;
        }
    }
});

dec.factory('DocumentosWorkflow', function() {
    var documentos_workflow=[];

    return {
        setDocumentosWorkflow : function(d) {
            documentos_workflow=[];
            for(i=0;i<d.length;i++)
            {
                subtipos=d[i].subtipoDocumentos;
                if(subtipos!=null)
                {
                    for(j=0;j<subtipos.length;j++)
                    {
                        documentos_workflow.push(subtipos[j]);
                    }
                }
            }
        },
        addDocumentoWorkflow : function(d)
        {
            documentos_workflow.push(d);
        },
        getWorkflow : function(id) {
            for(i=0;i<documentos_workflow.length;i++)
            {
                if(documentos_workflow[i].codigo==id)
                    return documentos_workflow[i];
            }
            return null;
        }
    }
});

dec.factory('DocumentosPendientes', function() {
    var documentos_pendientes=[];

    return {
        setDocumentosPendientes : function(d) {
            documentos_pendientes=d;
        },
        getDocumentosPendientes : function() {
            return documentos_pendientes;
        },
        getDocumento : function(id) {
            for(i=0;i<documentos_pendientes.length;i++)
            {
                if(documentos_pendientes[i].idAcepta==id)
                    return documentos_pendientes[i];
            }
            return null;
        }
    }
});

dec.filter('trusted', ['$sce', function ($sce) {
    return function(url) {
        return $sce.trustAsResourceUrl(url);
    };
}]);