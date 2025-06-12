
CREATE TABLE atributes
(
  id         BIGINT       NULL     AUTO_INCREMENT,
  name       VARCHAR(255) NOT NULL,
  icon       VARCHAR(255) NULL    ,
  slug       VARCHAR(255) NOT NULL,
  is_global  TINYINT(1)   NULL     DEFAULT 0,
  created_at TIMESTAMP    NULL    ,
  updated_at TIMESTAMP    NULL    ,
  PRIMARY KEY (id)
);

CREATE TABLE attribute_values
(
  id           BIGINT       NULL     AUTO_INCREMENT,
  atributes_id BIGINT       NOT NULL,
  value        VARCHAR(255) NOT NULL,
  created_at   TIMESTAMP    NULL    ,
  updated_at   TIMESTAMP    NULL    ,
  ON                        NULL    ,
  PRIMARY KEY (id)
);

CREATE TABLE categories
(
  id          BIGINT       NULL     AUTO_INCREMENT,
  name        VARCHAR(255) NOT NULL,
  slug        VARCHAR(255) NOT NULL,
  description TEXT         NULL    ,
  image       VARCHAR(255) NULL    ,
  icon        VARCHAR(255) NULL    ,
  parent_id   BIGINT       NULL    ,
  section_id  BIGINT       NOT NULL,
  created_at  TIMESTAMP    NULL    ,
  updated_at  TIMESTAMP    NULL    ,
  ON                       NULL    ,
  ON                       NULL    ,
  PRIMARY KEY (id)
);

ALTER TABLE categories
  ADD CONSTRAINT UQ_slug UNIQUE (slug);

CREATE TABLE category_attributes
(
  id           BIGINT    NULL     AUTO_INCREMENT,
  category_id  BIGINT    NOT NULL,  
  atributes_id BIGINT    NOT NULL,
  created_at   TIMESTAMP NULL    ,
  updated_at   TIMESTAMP NULL    ,
  ON                     NULL    ,
  ON                     NULL    ,
  PRIMARY KEY (id)
);

CREATE TABLE favorites
(
  id         BIGINT    NULL     AUTO_INCREMENT,
  user_id    BIGINT    NOT NULL,
  product_id BIGINT    NOT NULL,
  created_at TIMESTAMP NULL    ,
  updated_at TIMESTAMP NULL    ,
  ON                   NULL    ,
  ON                   NULL    ,
  PRIMARY KEY (id)
);

CREATE TABLE order_items
(
  id         BIGINT        NULL     AUTO_INCREMENT,
  order_id   BIGINT        NOT NULL,
  product_id BIGINT        NOT NULL,
  quantity   INT           NOT NULL,
  price      DECIMAL(10,2) NOT NULL,
  updated_at TIMESTAMP     NULL    ,
  ON                       NULL    ,
  ON                       NULL    ,
  created_at TIMESTAMP     NULL    ,
  PRIMARY KEY (id)
);

CREATE TABLE orders
(
  id         BIGINT        NULL     AUTO_INCREMENT,
  user_id    BIGINT        NOT NULL,
  total      DECIMAL(10,2) NOT NULL,
  status     VARCHAR(255)  NULL     DEFAULT pending,
  created_at TIMESTAMP     NULL    ,
  updated_at TIMESTAMP     NULL    ,
  ON                       NULL    ,
  PRIMARY KEY (id)
);

CREATE TABLE plans
(
  id            BIGINT                       NULL     AUTO_INCREMENT,
  name          VARCHAR(255)                 NOT NULL,
  description   VARCHAR(255)                 NULL    ,
  price         DECIMAL(10,2)                NULL     DEFAULT 0,
  commission    DECIMAL(5,2)                 NULL     DEFAULT 0,
  max_products  INT                          NULL     DEFAULT 10,
  max_images    INT                          NULL     DEFAULT 5,
  priority      INT                          NULL     DEFAULT 0,
  support_level ENUM(basic,standard,premium) NULL     DEFAULT basic,
  features      JSON                         NULL    ,
  is_active     TINYINT(1)                   NULL     DEFAULT 1,
  created_at    TIMESTAMP                    NULL    ,
  updated_at    TIMESTAMP                    NULL    ,
  PRIMARY KEY (id)
);

ALTER TABLE plans
  ADD CONSTRAINT UQ_name UNIQUE (name);

CREATE TABLE product_reference_attribute_values
(
  id                   BIGINT    NULL     AUTO_INCREMENT,
  product_reference_id BIGINT    NOT NULL,
  atributes_id         BIGINT    NOT NULL,
  attribute_value_id   BIGINT    NOT NULL,
  created_at           TIMESTAMP NULL    ,
  updated_at           TIMESTAMP NULL    ,
  ON                             NULL    ,
  ON                             NULL    ,
  ON                             NULL    ,
  PRIMARY KEY (id)
);

ALTER TABLE product_reference_attribute_values
  ADD CONSTRAINT UQ_product_reference_id UNIQUE (product_reference_id);

ALTER TABLE product_reference_attribute_values
  ADD CONSTRAINT UQ_atributes_id UNIQUE (atributes_id);

CREATE TABLE product_references
(
  id             BIGINT       NULL     AUTO_INCREMENT,
  category_id    BIGINT       NOT NULL,
  name           VARCHAR(255) NOT NULL,
  slug           VARCHAR(255) NOT NULL,
  description    TEXT         NOT NULL,
  main_image     VARCHAR(255) NULL    ,
  gallery_images JSON         NULL    ,
  reference_code VARCHAR(255) NULL    ,
  created_at     TIMESTAMP    NULL    ,
  updated_at     TIMESTAMP    NULL    ,
  ON                          NULL    ,
  PRIMARY KEY (id)
);

ALTER TABLE product_references
  ADD CONSTRAINT UQ_slug UNIQUE (slug);

ALTER TABLE product_references
  ADD CONSTRAINT UQ_reference_code UNIQUE (reference_code);

CREATE TABLE product_references_variants
(
  id                    BIGINT        NULL     AUTO_INCREMENT,
  product_references_id BIGINT        NOT NULL,
  sku                   VARCHAR(255)  NOT NULL,
  price                 DECIMAL(10,2) NOT NULL,
  stock                 INT           NOT NULL,
  image_path            VARCHAR(255)  NULL    ,
  created_at            TIMESTAMP     NULL    ,
  updated_at            TIMESTAMP     NULL    ,
  ON                                  NULL    ,
  PRIMARY KEY (id)
);

ALTER TABLE product_references_variants
  ADD CONSTRAINT UQ_sku UNIQUE (sku);

CREATE TABLE product_shipping
(
  id         BIGINT       NULL     AUTO_INCREMENT,
  product_id BIGINT       NOT NULL,
  weight     DECIMAL(8,2) NOT NULL,
  height     DECIMAL(8,2) NOT NULL,
  width      DECIMAL(8,2) NOT NULL,
  length     DECIMAL(8,2) NOT NULL,
  carrier    VARCHAR(255) NOT NULL,
  created_at TIMESTAMP    NULL    ,
  updated_at TIMESTAMP    NULL    ,
  ON                      NULL    ,
  PRIMARY KEY (id)
);

CREATE TABLE products
(
  id                   BIGINT                         NULL     AUTO_INCREMENT,
  seller_id            BIGINT                         NOT NULL,
  plan_id              BIGINT                         NOT NULL,
  product_reference_id BIGINT                         NULL    ,
  price                DECIMAL(10,2)                  NOT NULL,
  discount_price       DECIMAL(10,2)                  NULL    ,
  stock                INT                            NULL     DEFAULT 0,
  is_active            TINYINT(1)                     NULL     DEFAULT 1,
  is_featured          TINYINT(1)                     NULL     DEFAULT 0,
  is_new               TINYINT(1)                     NULL     DEFAULT 0,
  free_shipping        TINYINT(1)                     NULL     DEFAULT 0,
  shipping_cost        DECIMAL(10,2)                  NULL    ,
  sku                  VARCHAR(255)                   NULL    ,
  condition            VARCHAR(255)                   NULL     DEFAULT new,
  status               ENUM(pending,published,paused) NULL     DEFAULT pending,
  views                INT                            NULL     DEFAULT 0,
  sales_count          INT                            NULL     DEFAULT 0,
  rating               DECIMAL(3,2)                   NULL     DEFAULT 0,
  is_best_seller       TINYINT(1)                     NULL     DEFAULT 0,
  is_deal_of_the_day   TINYINT(1)                     NULL     DEFAULT 0,
  warranty_value       INT                            NULL    ,
  warranty_unit        VARCHAR(255)                   NULL    ,
  created_at           TIMESTAMP                      NULL    ,
  updated_at           TIMESTAMP                      NULL    ,
  ON                                                  NULL    ,
  ON                                                  NULL    ,
  ON                   SET                            NULL    ,
  PRIMARY KEY (id)
);

ALTER TABLE products
  ADD CONSTRAINT UQ_seller_id UNIQUE (seller_id);

ALTER TABLE products
  ADD CONSTRAINT UQ_product_reference_id UNIQUE (product_reference_id);

CREATE TABLE reviews
(
  id         BIGINT    NULL     AUTO_INCREMENT,
  user_id    BIGINT    NOT NULL,
  product_id BIGINT    NOT NULL,
  rating     INT       NOT NULL,
  comment    TEXT      NULL    ,
  created_at TIMESTAMP NULL    ,
  updated_at TIMESTAMP NULL    ,
  ON                   NULL    ,
  ON                   NULL    ,
  PRIMARY KEY (id)
);

CREATE TABLE sections
(
  id         BIGINT       NULL     AUTO_INCREMENT,
  name       VARCHAR(255) NOT NULL,
  slug       VARCHAR(255) NOT NULL,
  created_at TIMESTAMP    NULL    ,
  updated_at TIMESTAMP    NULL    ,
  PRIMARY KEY (id)
);

ALTER TABLE sections
  ADD CONSTRAINT UQ_slug UNIQUE (slug);

CREATE TABLE sellers
(
  id          BIGINT       NULL     AUTO_INCREMENT,
  user_id     BIGINT       NOT NULL,
  store_name  VARCHAR(255) NOT NULL,
  slug        VARCHAR(255) NOT NULL,
  description TEXT         NULL    ,
  logo        VARCHAR(255) NULL    ,
  banner      VARCHAR(255) NULL    ,
  sales_count VARCHAR(255) NULL    ,
  is_active   TINYINT(1)   NULL     DEFAULT 1,
  created_at  TIMESTAMP    NULL    ,
  updated_at  TIMESTAMP    NULL    ,
  ON                       NULL    ,
  PRIMARY KEY (id)
);

ALTER TABLE sellers
  ADD CONSTRAINT UQ_store_name UNIQUE (store_name);

ALTER TABLE sellers
  ADD CONSTRAINT UQ_slug UNIQUE (slug);

CREATE TABLE transactions
(
  id             BIGINT        NULL     AUTO_INCREMENT,
  user_id        BIGINT        NOT NULL,
  order_id       BIGINT        NOT NULL,
  payment_method VARCHAR(255)  NOT NULL,
  status         VARCHAR(255)  NULL     DEFAULT pending,
  amount         DECIMAL(10,2) NOT NULL,
  created_at     TIMESTAMP     NULL    ,
  updated_at     TIMESTAMP     NULL    ,
  ON                           NULL    ,
  ON                           NULL    ,
  PRIMARY KEY (id)
);

CREATE TABLE users
(
  id                        BIGINT       NULL     AUTO_INCREMENT,
  name                      VARCHAR(255) NOT NULL,
  last_name                 VARCHAR(255) NULL    ,
  email                     VARCHAR(255) NOT NULL,
  email_verified_at         TIMESTAMP    NULL    ,
  password                  VARCHAR(255) NULL    ,
  password_reset_token      VARCHAR(255) NULL    ,
  avatar                    VARCHAR(255) NULL    ,
  password_reset_expires_at TIMESTAMP    NULL    ,
  is_seller                 TINYINT(1)   NULL     DEFAULT 0,
  remember_token            VARCHAR(100) NULL    ,
  created_at                TIMESTAMP    NULL    ,
  updated_at                TIMESTAMP    NULL    ,
  PRIMARY KEY (id)
);

ALTER TABLE users
  ADD CONSTRAINT UQ_email UNIQUE (email);

CREATE TABLE variant_attribute_values
(
  id                           BIGINT    NULL     AUTO_INCREMENT,
  product_reference_variant_id BIGINT    NOT NULL,
  attribute_value_id           BIGINT    NOT NULL,
  created_at                   TIMESTAMP NULL    ,
  updated_at                   TIMESTAMP NULL    ,
  ON                                     NULL    ,
  ON                                     NULL    ,
  PRIMARY KEY (id)
);

ALTER TABLE sellers
  ADD CONSTRAINT FK_users_TO_sellers
    FOREIGN KEY (user_id)
    REFERENCES users (id);

ALTER TABLE categories
  ADD CONSTRAINT FK_categories_TO_categories
    FOREIGN KEY (parent_id)
    REFERENCES categories (id);

ALTER TABLE categories
  ADD CONSTRAINT FK_sections_TO_categories
    FOREIGN KEY (section_id)
    REFERENCES sections (id);

ALTER TABLE product_references
  ADD CONSTRAINT FK_categories_TO_product_references
    FOREIGN KEY (category_id)
    REFERENCES categories (id);

ALTER TABLE products
  ADD CONSTRAINT FK_sellers_TO_products
    FOREIGN KEY (seller_id)
    REFERENCES sellers (id);

ALTER TABLE products
  ADD CONSTRAINT FK_plans_TO_products
    FOREIGN KEY (plan_id)
    REFERENCES plans (id);

ALTER TABLE products
  ADD CONSTRAINT FK_product_references_TO_products
    FOREIGN KEY (product_reference_id)
    REFERENCES product_references (id);

ALTER TABLE orders
  ADD CONSTRAINT FK_users_TO_orders
    FOREIGN KEY (user_id)
    REFERENCES users (id);

ALTER TABLE order_items
  ADD CONSTRAINT FK_orders_TO_order_items
    FOREIGN KEY (order_id)
    REFERENCES orders (id);

ALTER TABLE order_items
  ADD CONSTRAINT FK_products_TO_order_items
    FOREIGN KEY (product_id)
    REFERENCES products (id);

ALTER TABLE transactions
  ADD CONSTRAINT FK_users_TO_transactions
    FOREIGN KEY (user_id)
    REFERENCES users (id);

ALTER TABLE transactions
  ADD CONSTRAINT FK_orders_TO_transactions
    FOREIGN KEY (order_id)
    REFERENCES orders (id);

ALTER TABLE reviews
  ADD CONSTRAINT FK_users_TO_reviews
    FOREIGN KEY (user_id)
    REFERENCES users (id);

ALTER TABLE reviews
  ADD CONSTRAINT FK_products_TO_reviews
    FOREIGN KEY (product_id)
    REFERENCES products (id);

ALTER TABLE favorites
  ADD CONSTRAINT FK_users_TO_favorites
    FOREIGN KEY (user_id)
    REFERENCES users (id);

ALTER TABLE favorites
  ADD CONSTRAINT FK_products_TO_favorites
    FOREIGN KEY (product_id)
    REFERENCES products (id);

ALTER TABLE category_attributes
  ADD CONSTRAINT FK_categories_TO_category_attributes
    FOREIGN KEY (category_id)
    REFERENCES categories (id);

ALTER TABLE category_attributes
  ADD CONSTRAINT FK_atributes_TO_category_attributes
    FOREIGN KEY (atributes_id)
    REFERENCES atributes (id);

ALTER TABLE product_references_variants
  ADD CONSTRAINT FK_product_references_TO_product_references_variants
    FOREIGN KEY (product_references_id)
    REFERENCES product_references (id);

ALTER TABLE attribute_values
  ADD CONSTRAINT FK_atributes_TO_attribute_values
    FOREIGN KEY (atributes_id)
    REFERENCES atributes (id);

ALTER TABLE product_reference_attribute_values
  ADD CONSTRAINT FK_product_references_TO_product_reference_attribute_values
    FOREIGN KEY (product_reference_id)
    REFERENCES product_references (id);

ALTER TABLE product_reference_attribute_values
  ADD CONSTRAINT FK_atributes_TO_product_reference_attribute_values
    FOREIGN KEY (atributes_id)
    REFERENCES atributes (id);

ALTER TABLE product_reference_attribute_values
  ADD CONSTRAINT FK_attribute_values_TO_product_reference_attribute_values
    FOREIGN KEY (attribute_value_id)
    REFERENCES attribute_values (id);

ALTER TABLE variant_attribute_values
  ADD CONSTRAINT FK_product_references_variants_TO_variant_attribute_values
    FOREIGN KEY (product_reference_variant_id)
    REFERENCES product_references_variants (id);

ALTER TABLE variant_attribute_values
  ADD CONSTRAINT FK_attribute_values_TO_variant_attribute_values
    FOREIGN KEY (attribute_value_id)
    REFERENCES attribute_values (id);

ALTER TABLE product_shipping
  ADD CONSTRAINT FK_products_TO_product_shipping
    FOREIGN KEY (product_id)
    REFERENCES products (id);
