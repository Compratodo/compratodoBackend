dbcompratodomigrations-- Tabla de usuarios
CREATE TABLE users (
  id                        BIGINT AUTO_INCREMENT,                              
  name                      VARCHAR(255) NOT NULL,                              
  last_name                 VARCHAR(255),                                       
  email                     VARCHAR(255) NOT NULL,                              
  email_verified_at         TIMESTAMP NULL,                                     
  password                  VARCHAR(255) DEFAULT NULL,                          
  avatar                    VARCHAR(255),                                       
  provider                  ENUM('google', 'facebook') DEFAULT NULL,
  provider_id               VARCHAR(255),
  accepted_terms            TINYINT(1) NOT NULL DEFAULT 0,
  remember_token            VARCHAR(100),
  created_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE (email),
  CHECK (accepted_terms = 1)
);

CREATE TABLE sellers (
  id                        BIGINT AUTO_INCREMENT,
  user_id                   BIGINT NOT NULL,
  store_name                VARCHAR(255) NOT NULL,
  slug                      VARCHAR(255) NOT NULL,
  description               TEXT,
  logo                      VARCHAR(255),
  banner                    VARCHAR(255),
  sales_count               VARCHAR(255),
  is_active                 TINYINT(1) NOT NULL DEFAULT 1,
  created_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),                                                     
  UNIQUE (store_name),                                                  
  UNIQUE (slug),                                                        
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabla de verificación por correo
CREATE TABLE email_verifications (
  id          BIGINT AUTO_INCREMENT,
  user_id     BIGINT NOT NULL,
  code        VARCHAR(12) NOT NULL,
  expires_at  TIMESTAMP NOT NULL,
  verified_at TIMESTAMP NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabla de verificación por SMS
CREATE TABLE sms_verifications (
  id          BIGINT AUTO_INCREMENT,
  user_id     BIGINT NOT NULL,
  phone       VARCHAR(10) NOT NULL,
  code        VARCHAR(6) NOT NULL,
  expires_at  TIMESTAMP NOT NULL,
  verified_at TIMESTAMP NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabla de preguntas y respuestas de seguridad
CREATE TABLE security_questions (
  id          BIGINT AUTO_INCREMENT,
  user_id     BIGINT NOT NULL,
  question    VARCHAR(255) NOT NULL,
  answer      VARCHAR(255) NOT NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabla para recuperación de contraseña
CREATE TABLE password_resets (
  id          BIGINT AUTO_INCREMENT,
  user_id     BIGINT NOT NULL,
  token       VARCHAR(255) NOT NULL,
  expires_at  TIMESTAMP NOT NULL,
  used_at     TIMESTAMP NULL,
  method      ENUM('email', 'sms', 'question') NOT NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);