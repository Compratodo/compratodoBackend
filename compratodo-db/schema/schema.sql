-- Tabla de usuarios
CREATE TABLE users (
  id                        BIGINT AUTO_INCREMENT,                              -- Id unico para cada usuario, tipo BIGINT, se incrementa automaticamente 
  name                      VARCHAR(255) NOT NULL,                              -- Nombre del usuario, (obligatorio)
  last_name                 VARCHAR(255),                                       -- Apellido del usuario, (opcional)
  email                     VARCHAR(255) NOT NULL,                              -- Correo del usuario, obligatorio y unico 
  email_verified_at         TIMESTAMP NULL,                                     -- Fecha y hora en que el correo fue verificado, puede ser null si no ha sido verificado
  password                  VARCHAR(255) DEFAULT NULL,                          -- Contrasena del usuario, puede ser nula si se usa un proveedor externo (google/facebook)
  avatar                    VARCHAR(255),                                       -- Url o ruta del avatar del usuario
  provider                  ENUM('google', 'facebook') DEFAULT NULL,            -- Tipo de proveedor externo usado para autenticacion (facebook/google), puede ser nulo
  provider_id               VARCHAR(255),                                       -- Id del usuario del proveedor externo
  accepted_terms            TINYINT(1) NOT NULL DEFAULT 0,                      -- Indica si el usuario acepto los terminos (1=si / 0=no) obligatorio y por defecto en 0
  remember_token            VARCHAR(100),                                       -- Token para recordar la sesion del usuario (funcion 'recuerdame')
  created_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP,                -- Fecha de Creacion
  updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,        -- Fecha de actualizacion
  PRIMARY KEY (id),                                                             -- Se define el campo 'id' como clave primaria. (no se puede repetir valores y es unico)
  UNIQUE (email),                                                               -- Se asegura email unico (no se repite entre usuarios)
  CHECK (accepted_terms = 1)                                                    -- Restriccion para asegurar que 'accepted_terms' solo puede ser 1 (obliga a aceptar terminos)
);

CREATE TABLE sellers (
  id                        BIGINT AUTO_INCREMENT,                      -- Identificador unico de la tienda
  user_id                   BIGINT NOT                                  -- ID del usuario dueno de la tienda
  store_name                VARCHAR(255) NOT NULL,                      -- Nombre visible de la tienda, (obligatorio)
  slug                      VARCHAR(255) NOT NULL,                      -- Version Url segun el nombre, (obligatorio)
  description               TEXT,                                       -- Descripcion de la tienda opcional
  logo                      VARCHAR(255),                               -- Url o nombre del archivo del logo
  banner                    VARCHAR(255),                               -- Imagen de portada/banner de la tienda
  sales_count               VARCHAR(255),                               -- Cantidad de ventas
  is_active                 TINYINT(1) NOT NULL DEFAULT 1,              -- Si la tienda esta activa (1) o desactiva (0)
  created_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP,        -- Fecha de creacion
  updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,    --Fecha de actualizacion
  PRIMARY KEY (id),                                                     -- Se define el campo 'id' como clave primaria. (no se puede repetir valores y es unico)
  UNIQUE (store_name),                                                  -- Se asegura nombre de la tienda unico (no se repite entre usuarios)
  UNIQUE (slug),                                                        -- Se asegura version url unica (no se repite entre usuarios)
  FOREIGN KEY (user_id) REFERENCES users(id),
);

-- Tabla de verificación por correo
CREATE TABLE email_verifications (
  id          BIGINT AUTO_INCREMENT,                                    -- Id unico para cada verificacion de correo, tipo BIGINT, se incrementa automaticamente 
  user_id     BIGINT NOT NULL,                                          -- Id del usuario al que pertenece el codigo de verificacion, (obligatorio)
  code        VARCHAR(12) NOT NULL,                                      -- Codigo de verificacion (6 digitos), (obligatorio)
  expires_at  TIMESTAMP NOT NULL,                                       -- fecha y hora en la que expira el codigo de verificacion, (obligatorio)
  verified_at TIMESTAMP NULL,                                           -- fecha y hora en la que el codigo fue verificado, (null si no esta verificado)
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,                      -- Fecha de creacion del registro
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,      --Fecha de actualizacion del registro
  PRIMARY KEY (id),                                                     -- Se define el campo 'id' como clave primaria
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE          -- Se define clave foranea que enlaza con 'user_id', de la tabla 'users', si se elimina el usuario...
                                                                        -- ... tambien se eliminan sus verificaciones
);

-- Tabla de verificación por SMS
CREATE TABLE sms_verifications (
  id          BIGINT AUTO_INCREMENT,                                    -- Id unico para cada verificacion por sms, tipo BIGINT, se incrementa automaticamente
  user_id     BIGINT NOT NULL,                                          -- Id del usuario al que pertenece el codigo de verificacion (obligatorio)
  phone       VARCHAR(10) NOT NULL,                                     -- Numero de telefono al que se envia el codigo (obligatorio)
  code        VARCHAR(6) NOT NULL,                                      -- Codigo de verificacion (obligatorio)
  expires_at  TIMESTAMP NOT NULL,                                       -- Fecha y hora de expiracion del codigo (obligatorio)
  verified_at TIMESTAMP NULL,                                           -- Fecha y hora de verificacion del codigo (null si no se ha verificado)
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,                      -- Fecha de creacion del registro, se establece automaticamente al insertar
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,      --Fecha de ultima actualizacion del registro (se actualiza automaticamente)
  PRIMARY KEY (id),                                                     -- Se define 'id' como clave primaria
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE          -- Se define clave foranea que enlaza con 'user_id', de la tabla 'users', si se elimina el usuario...
                                                                        -- ... tambien se eliminan sus verificaciones
);

-- Tabla de preguntas y respuestas de seguridad
CREATE TABLE security_questions (
  id          BIGINT AUTO_INCREMENT,                                    -- Id unico para cada verificacion por sms, tipo BIGINT, se incrementa automaticamente
  user_id     BIGINT NOT NULL,                                          -- Id del usuario al que pertenece el codigo de verificacion, obligatorio
  question    VARCHAR(255) NOT NULL,                                    -- Texto de la pregunta de seguridad (obligatorio)
  answer      VARCHAR(255) NOT NULL,                                    -- Respuesta a la pregunta de seguridad, (obligatorio)
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,                      -- Fecha de creacion del registro (se crea automaticamente al insertar)
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,      -- Fecha de ultima actualizacion del registro (se actualiza automaticamente)
  PRIMARY KEY (id),                                                     -- Se define 'id' como clave primaria
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE          -- Se define clave foranea que enlaza con 'user_id', de la tabla 'users', si se elimina el usuario...
                                                                        -- ... tambien se eliminan sus verificaciones
);

-- Tabla para recuperación de contraseña
CREATE TABLE password_resets (
  id          BIGINT AUTO_INCREMENT,                                    -- Id Unico para cada intento de restablecimiento de contrasena,(se incrementa automaticamente)
  user_id     BIGINT NOT NULL,                                          -- Id del usuario que solicita el restablecimiento de la contrasena (obligatorio)
  token       VARCHAR(255) NOT NULL,                                    -- Token unico generado para reestablecer contrasena, (obligatorio)
  expires_at  TIMESTAMP NOT NULL,                                       -- Fecha y Hora en la que expira el token, (obligatorio)
  used_at     TIMESTAMP NULL,                                           -- Fecha y hora en la que el token fue usado (puede ser null si no se ha usado el token)
  method      ENUM('email', 'sms', 'question') NOT NULL,                -- Metodo utilizado para la recuperacion ('email', 'sms', 'question'), obligatorio
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,                      -- Fecha de creacion del registro
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,      -- fecha ultima actualizacion del registro
  PRIMARY KEY (id),                                                     -- Se define 'id' como clave primaria
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE          -- Se define clave foranea que enlaza con 'user_id', de la tabla 'users', si se elimina el usuario...
                                                                        -- ... tambien se eliminan sus verificaciones
);
