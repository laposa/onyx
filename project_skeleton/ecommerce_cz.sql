--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: acl_operation; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE acl_operation AS ENUM (
    '_all_',
    'view',
    'edit',
    'add',
    'delete',
    'publish'
);


--
-- Name: acl_resource; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE acl_resource AS ENUM (
    '_all_',
    'front_office',
    'back_office',
    'nodes',
    'products',
    'recipes',
    'stores',
    'orders',
    'stock',
    'customers',
    'reports',
    'discounts',
    'comments',
    'surveys',
    'media',
    'taxonomy',
    'seo_manager',
    'database',
    'templates',
    'scheduler',
    'currency',
    'search_index',
    'tools',
    'logs',
    'configuration',
    'permissions'
);


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: client_action; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE client_action (
    id integer NOT NULL,
    customer_id integer NOT NULL,
    node_id integer NOT NULL,
    action_id character varying(255),
    network character varying(255),
    action_name character varying(255),
    object_name character varying(255),
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    other_data text
);


--
-- Name: client_action_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE client_action_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: client_action_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE client_action_id_seq OWNED BY client_action.id;


--
-- Name: client_address; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE client_address (
    id integer NOT NULL,
    customer_id integer,
    country_id integer,
    name character varying(255),
    line_1 character varying(255),
    line_2 character varying(255),
    line_3 character varying(255),
    post_code character varying(255),
    city character varying(255),
    county character varying(255),
    telephone character varying(255),
    comment character varying(255),
    is_deleted boolean DEFAULT false
);


--
-- Name: client_address_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE client_address_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: client_address_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE client_address_id_seq OWNED BY client_address.id;


--
-- Name: client_company; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE client_company (
    id integer NOT NULL,
    name character varying(255),
    www character varying(255),
    telephone character varying(255),
    fax character varying(255),
    customer_id integer,
    registration_no character varying(255),
    vat_no character varying(255),
    other_data text
);


--
-- Name: client_company_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE client_company_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: client_company_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE client_company_id_seq OWNED BY client_company.id;


--
-- Name: client_customer; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE client_customer (
    id integer NOT NULL,
    title_before character varying(255),
    first_name character varying(255),
    last_name character varying(255),
    title_after character varying(255),
    email character varying(255),
    username character varying(255),
    telephone character varying(255),
    mobilephone character varying(255),
    nickname character varying(255),
    password character varying(255),
    company_id integer,
    invoices_address_id integer,
    delivery_address_id integer,
    gender character(1),
    created timestamp(0) without time zone,
    currency_code character(3),
    status smallint,
    newsletter smallint,
    birthday date,
    other_data text,
    modified timestamp(0) without time zone,
    account_type smallint DEFAULT 0 NOT NULL,
    agreed_with_latest_t_and_c smallint DEFAULT 0 NOT NULL,
    verified_email_address smallint DEFAULT 0 NOT NULL,
    oauth text,
    deleted_date timestamp without time zone,
    facebook_id character varying(255),
    twitter_id character varying(255),
    google_id character varying(255),
    profile_image_url text,
    store_id integer,
    janrain_id character varying(255)
);


--
-- Name: client_customer_group; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE client_customer_group (
    id integer NOT NULL,
    group_id integer NOT NULL,
    customer_id integer NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    modified timestamp without time zone DEFAULT now() NOT NULL
);


--
-- Name: client_customer_group_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE client_customer_group_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: client_customer_group_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE client_customer_group_id_seq OWNED BY client_customer_group.id;


--
-- Name: client_customer_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE client_customer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: client_customer_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE client_customer_id_seq OWNED BY client_customer.id;


--
-- Name: client_customer_image; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE client_customer_image (
    id integer NOT NULL,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer,
    content text,
    other_data text,
    link_to_node_id integer,
    customer_id integer
);


--
-- Name: client_customer_image_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE client_customer_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: client_customer_image_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE client_customer_image_id_seq OWNED BY client_customer_image.id;


--
-- Name: client_customer_role; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE client_customer_role (
    id integer NOT NULL,
    role_id integer NOT NULL,
    customer_id integer NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    modified timestamp without time zone DEFAULT now() NOT NULL
);


--
-- Name: client_customer_role_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE client_customer_role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: client_customer_role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE client_customer_role_id_seq OWNED BY client_customer_role.id;


--
-- Name: client_customer_taxonomy; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE client_customer_taxonomy (
    id integer NOT NULL,
    node_id integer NOT NULL,
    taxonomy_tree_id integer NOT NULL
);


--
-- Name: client_customer_taxonomy_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE client_customer_taxonomy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: client_customer_taxonomy_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE client_customer_taxonomy_id_seq OWNED BY client_customer_taxonomy.id;


--
-- Name: client_customer_token; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE client_customer_token (
    id integer NOT NULL,
    customer_id integer NOT NULL,
    publish smallint DEFAULT 0 NOT NULL,
    token character(32),
    oauth_data text,
    other_data text,
    ttl integer,
    ip_address character varying(255),
    http_user_agent character varying(255),
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


--
-- Name: client_customer_token_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE client_customer_token_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: client_customer_token_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE client_customer_token_id_seq OWNED BY client_customer_token.id;


--
-- Name: client_group; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE client_group (
    id integer NOT NULL,
    name character varying(255),
    description text,
    search_filter text,
    other_data text
);


--
-- Name: client_group_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE client_group_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: client_group_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE client_group_id_seq OWNED BY client_group.id;


--
-- Name: client_role; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE client_role (
    id integer NOT NULL,
    name character varying(255),
    description text,
    other_data text
);


--
-- Name: client_role_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE client_role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: client_role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE client_role_id_seq OWNED BY client_role.id;


--
-- Name: client_role_permission; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE client_role_permission (
    id integer NOT NULL,
    role_id integer NOT NULL,
    resource acl_resource,
    operation acl_operation,
    scope text,
    created timestamp without time zone DEFAULT now() NOT NULL,
    modified timestamp without time zone DEFAULT now() NOT NULL,
    other_data text
);


--
-- Name: client_role_permission_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE client_role_permission_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: client_role_permission_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE client_role_permission_id_seq OWNED BY client_role_permission.id;


--
-- Name: common_comment; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE common_comment (
    id integer NOT NULL,
    parent integer,
    node_id integer,
    title character varying(255),
    content text,
    author_name character varying(255),
    author_email character varying(255),
    author_website character varying(255),
    author_ip_address character varying(255),
    customer_id integer NOT NULL,
    created timestamp(0) without time zone DEFAULT now(),
    publish smallint,
    rating smallint DEFAULT 0,
    relation_subject text
);


--
-- Name: common_comment_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE common_comment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: common_comment_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE common_comment_id_seq OWNED BY common_comment.id;


--
-- Name: common_configuration; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE common_configuration (
    id integer NOT NULL,
    node_id integer DEFAULT 0 NOT NULL,
    object character varying(255),
    property character varying(255),
    value text,
    description text,
    apply_to_children smallint DEFAULT (0)::smallint
);


--
-- Name: common_configuration_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE common_configuration_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: common_configuration_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE common_configuration_id_seq OWNED BY common_configuration.id;


--
-- Name: common_email; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE common_email (
    id integer NOT NULL,
    email_from character varying(255),
    name_from character varying(255),
    subject character varying(255),
    content text,
    template character varying(255),
    email_recipient character varying(255),
    name_recipient character varying(255),
    created timestamp(0) without time zone,
    ip character varying(255)
);


--
-- Name: common_email_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE common_email_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: common_email_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE common_email_id_seq OWNED BY common_email.id;


--
-- Name: common_file; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE common_file (
    id integer NOT NULL,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer,
    content text,
    other_data text,
    link_to_node_id integer,
    customer_id integer
);


--
-- Name: common_file_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE common_file_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: common_file_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE common_file_id_seq OWNED BY common_file.id;


--
-- Name: common_image; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE common_image (
    id integer NOT NULL,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer,
    content text,
    other_data text,
    link_to_node_id integer,
    customer_id integer
);


--
-- Name: common_image_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE common_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: common_image_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE common_image_id_seq OWNED BY common_image.id;


--
-- Name: common_node; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE common_node (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    node_group character varying(255) NOT NULL,
    node_controller character varying(255),
    parent integer,
    parent_container smallint DEFAULT 0 NOT NULL,
    priority integer DEFAULT 0 NOT NULL,
    strapline text,
    content text,
    description text,
    keywords text,
    page_title character varying(255),
    head text,
    created timestamp(0) without time zone DEFAULT now() NOT NULL,
    modified timestamp(0) without time zone DEFAULT now() NOT NULL,
    publish integer DEFAULT 0 NOT NULL,
    display_in_menu smallint DEFAULT 1 NOT NULL,
    author integer NOT NULL,
    uri_title character varying(255),
    display_permission smallint DEFAULT 0 NOT NULL,
    other_data text,
    css_class character varying(255) DEFAULT ''::character varying NOT NULL,
    layout_style character varying(255) DEFAULT ''::character varying NOT NULL,
    component text,
    relations text,
    display_title smallint,
    display_secondary_navigation smallint,
    require_login smallint,
    display_breadcrumb smallint DEFAULT 0 NOT NULL,
    browser_title character varying(255) DEFAULT ''::character varying NOT NULL,
    link_to_node_id integer DEFAULT 0 NOT NULL,
    require_ssl smallint DEFAULT 0 NOT NULL,
    display_permission_group_acl text,
    share_counter integer DEFAULT 0 NOT NULL,
    customer_id integer,
    custom_fields jsonb
);


--
-- Name: common_node_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE common_node_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: common_node_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE common_node_id_seq OWNED BY common_node.id;


--
-- Name: common_node_taxonomy; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE common_node_taxonomy (
    id integer NOT NULL,
    node_id integer NOT NULL,
    taxonomy_tree_id integer NOT NULL
);


--
-- Name: common_node_taxonomy_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE common_node_taxonomy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: common_node_taxonomy_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE common_node_taxonomy_id_seq OWNED BY common_node_taxonomy.id;


--
-- Name: common_print_article; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE common_print_article (
    id integer NOT NULL,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL,
    title character varying(255) NOT NULL,
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer,
    type character varying(255),
    authors text,
    issue_number integer,
    page_from integer,
    date date,
    other text
);


--
-- Name: common_print_article_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE common_print_article_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: common_print_article_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE common_print_article_id_seq OWNED BY common_print_article.id;


--
-- Name: common_revision; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE common_revision (
    id integer NOT NULL,
    object character varying(255) NOT NULL,
    node_id integer NOT NULL,
    content text,
    status smallint,
    customer_id integer,
    created timestamp without time zone DEFAULT now() NOT NULL,
    modified timestamp without time zone DEFAULT now() NOT NULL,
    other_data text
);


--
-- Name: common_revision_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE common_revision_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: common_revision_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE common_revision_id_seq OWNED BY common_revision.id;


--
-- Name: common_scheduler; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE common_scheduler (
    id integer NOT NULL,
    node_id integer,
    node_type character varying(255),
    controller character varying(255),
    parameters text,
    scheduled_time timestamp without time zone,
    status smallint,
    lock_token integer,
    result text,
    start_time timestamp without time zone,
    completed_time timestamp without time zone,
    created timestamp without time zone,
    modified timestamp without time zone DEFAULT now()
);


--
-- Name: common_scheduler_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE common_scheduler_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: common_scheduler_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE common_scheduler_id_seq OWNED BY common_scheduler.id;


--
-- Name: common_session; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE common_session (
    id integer NOT NULL,
    session_id character varying(32),
    session_data text,
    customer_id integer,
    created timestamp(0) without time zone,
    modified timestamp(0) without time zone,
    ip_address character varying(255),
    php_auth_user character varying(255),
    http_referer text,
    http_user_agent character varying(255)
);


--
-- Name: common_session_archive; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE common_session_archive (
    id integer NOT NULL,
    session_id character varying(32),
    session_data text,
    customer_id integer,
    created timestamp(0) without time zone,
    modified timestamp(0) without time zone,
    ip_address character varying(255),
    php_auth_user character varying(255),
    http_referer text,
    http_user_agent character varying(255)
);


--
-- Name: common_session_archive_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE common_session_archive_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: common_session_archive_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE common_session_archive_id_seq OWNED BY common_session_archive.id;


--
-- Name: common_session_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE common_session_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: common_session_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE common_session_id_seq OWNED BY common_session.id;


--
-- Name: common_taxonomy_label; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE common_taxonomy_label (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    description text,
    priority integer DEFAULT 0 NOT NULL,
    publish integer DEFAULT 1 NOT NULL
);


--
-- Name: common_taxonomy_label_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE common_taxonomy_label_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: common_taxonomy_label_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE common_taxonomy_label_id_seq OWNED BY common_taxonomy_label.id;


--
-- Name: common_taxonomy_label_image; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE common_taxonomy_label_image (
    id integer NOT NULL,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer,
    content text,
    other_data text,
    link_to_node_id integer,
    customer_id integer
);


--
-- Name: common_taxonomy_label_image_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE common_taxonomy_label_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: common_taxonomy_label_image_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE common_taxonomy_label_image_id_seq OWNED BY common_taxonomy_label_image.id;


--
-- Name: common_taxonomy_tree; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE common_taxonomy_tree (
    id integer NOT NULL,
    label_id integer NOT NULL,
    parent integer,
    priority smallint DEFAULT 0 NOT NULL,
    publish smallint DEFAULT 1 NOT NULL
);


--
-- Name: common_taxonomy_tree_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE common_taxonomy_tree_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: common_taxonomy_tree_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE common_taxonomy_tree_id_seq OWNED BY common_taxonomy_tree.id;


--
-- Name: common_uri_mapping; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE common_uri_mapping (
    id integer NOT NULL,
    node_id integer,
    public_uri text,
    type character varying(255)
);


--
-- Name: common_uri_mapping_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE common_uri_mapping_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: common_uri_mapping_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE common_uri_mapping_id_seq OWNED BY common_uri_mapping.id;


--
-- Name: common_watchdog; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE common_watchdog (
    id integer NOT NULL,
    name character varying(255),
    watched_item_id integer,
    customer_id integer,
    created timestamp without time zone,
    modified timestamp without time zone DEFAULT now(),
    publish smallint,
    other_data text
);


--
-- Name: common_watchdog_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE common_watchdog_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: common_watchdog_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE common_watchdog_id_seq OWNED BY common_watchdog.id;


--
-- Name: ecommerce_basket; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_basket (
    id integer NOT NULL,
    customer_id integer,
    created timestamp(0) without time zone,
    note text,
    ip_address character varying(255),
    face_value_voucher numeric(12,5) DEFAULT 0 NOT NULL,
    title character varying(255),
    other_data text
);


--
-- Name: ecommerce_basket_content; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_basket_content (
    id integer NOT NULL,
    basket_id integer,
    product_variety_id integer,
    quantity integer,
    price_id integer,
    other_data text,
    product_type_id smallint,
    discount numeric(12,5) DEFAULT 0 NOT NULL
);


--
-- Name: ecommerce_basket_content_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_basket_content_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_basket_content_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_basket_content_id_seq OWNED BY ecommerce_basket_content.id;


--
-- Name: ecommerce_basket_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_basket_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_basket_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_basket_id_seq OWNED BY ecommerce_basket.id;


--
-- Name: ecommerce_delivery; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_delivery (
    id integer NOT NULL,
    order_id integer,
    carrier_id integer,
    value_net numeric(12,5),
    vat numeric(12,5),
    vat_rate numeric(12,5),
    required_datetime timestamp(0) without time zone,
    note_customer text,
    note_backoffice text,
    other_data text,
    weight integer DEFAULT 0 NOT NULL
);


--
-- Name: ecommerce_delivery_carrier; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_delivery_carrier (
    id integer NOT NULL,
    title character varying(255),
    description text,
    priority smallint DEFAULT 0 NOT NULL,
    publish smallint DEFAULT 1 NOT NULL,
    zone_id integer,
    order_value_from numeric(12,5),
    order_value_to numeric(12,5)
);


--
-- Name: ecommerce_delivery_carrier_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_delivery_carrier_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_delivery_carrier_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_delivery_carrier_id_seq OWNED BY ecommerce_delivery_carrier.id;


--
-- Name: ecommerce_delivery_carrier_rate; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_delivery_carrier_rate (
    id integer NOT NULL,
    carrier_id integer,
    weight_from numeric(12,5) DEFAULT 0,
    weight_to numeric(12,5) DEFAULT 0,
    price numeric(12,5)
);


--
-- Name: ecommerce_delivery_carrier_rate_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_delivery_carrier_rate_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_delivery_carrier_rate_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_delivery_carrier_rate_id_seq OWNED BY ecommerce_delivery_carrier_rate.id;


--
-- Name: ecommerce_delivery_carrier_zone; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_delivery_carrier_zone (
    id integer NOT NULL,
    name character varying(255)
);


--
-- Name: ecommerce_delivery_carrier_zone_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_delivery_carrier_zone_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_delivery_carrier_zone_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_delivery_carrier_zone_id_seq OWNED BY ecommerce_delivery_carrier_zone.id;


--
-- Name: ecommerce_delivery_carrier_zone_to_country; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_delivery_carrier_zone_to_country (
    id integer NOT NULL,
    country_id integer NOT NULL,
    zone_id integer NOT NULL
);


--
-- Name: ecommerce_delivery_carrier_zone_to_country_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_delivery_carrier_zone_to_country_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_delivery_carrier_zone_to_country_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_delivery_carrier_zone_to_country_id_seq OWNED BY ecommerce_delivery_carrier_zone_to_country.id;


--
-- Name: ecommerce_delivery_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_delivery_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_delivery_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_delivery_id_seq OWNED BY ecommerce_delivery.id;


--
-- Name: ecommerce_invoice; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_invoice (
    id integer NOT NULL,
    order_id integer,
    goods_net numeric(12,5),
    goods_vat numeric(12,5),
    delivery_net numeric(12,5),
    delivery_vat numeric(12,5),
    payment_amount numeric(12,5),
    payment_type character varying(255),
    created timestamp(0) without time zone DEFAULT now() NOT NULL,
    modified timestamp(0) without time zone DEFAULT now() NOT NULL,
    status smallint,
    other_data text,
    basket_detail text,
    customer_name character varying(255),
    customer_email character varying(255),
    address_invoice text,
    address_delivery text,
    face_value_voucher numeric(12,5),
    basket_detail_enhanced text
);


--
-- Name: ecommerce_invoice_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_invoice_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_invoice_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_invoice_id_seq OWNED BY ecommerce_invoice.id;


--
-- Name: ecommerce_offer; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_offer (
    id integer NOT NULL,
    description text,
    product_variety_id integer,
    schedule_start timestamp(0) without time zone,
    schedule_end timestamp(0) without time zone,
    campaign_category_id integer,
    roundel_category_id integer,
    price_id integer,
    quantity integer,
    saving integer,
    created timestamp(0) without time zone,
    modified timestamp(0) without time zone,
    other_data text,
    offer_group_id integer,
    priority integer DEFAULT 0 NOT NULL
);


--
-- Name: ecommerce_offer_group; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_offer_group (
    id integer NOT NULL,
    title character varying(255),
    description text,
    schedule_start timestamp(0) without time zone,
    schedule_end timestamp(0) without time zone,
    publish integer DEFAULT 0 NOT NULL,
    created timestamp(0) without time zone,
    modified timestamp(0) without time zone,
    other_data text
);


--
-- Name: ecommerce_offer_group_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_offer_group_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_offer_group_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_offer_group_id_seq OWNED BY ecommerce_offer_group.id;


--
-- Name: ecommerce_offer_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_offer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_offer_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_offer_id_seq OWNED BY ecommerce_offer.id;


--
-- Name: ecommerce_order; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_order (
    id integer NOT NULL,
    basket_id integer,
    invoices_address_id integer,
    delivery_address_id integer,
    other_data text,
    status integer,
    note_customer text,
    note_backoffice text,
    php_session_id character varying(32),
    referrer character varying(255),
    payment_type character varying(255),
    created timestamp(0) without time zone DEFAULT now(),
    modified timestamp(0) without time zone DEFAULT now(),
    review_email_sent integer
);


--
-- Name: ecommerce_order_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_order_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_order_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_order_id_seq OWNED BY ecommerce_order.id;


--
-- Name: ecommerce_order_log; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_order_log (
    id integer NOT NULL,
    order_id integer,
    status integer,
    datetime timestamp(0) without time zone,
    description text,
    other_data text
);


--
-- Name: ecommerce_order_log_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_order_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_order_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_order_log_id_seq OWNED BY ecommerce_order_log.id;


--
-- Name: ecommerce_price; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_price (
    id integer NOT NULL,
    product_variety_id integer,
    currency_code character(3),
    value numeric(12,5),
    type character varying(255),
    date timestamp(0) without time zone
);


--
-- Name: ecommerce_price_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_price_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_price_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_price_id_seq OWNED BY ecommerce_price.id;


--
-- Name: ecommerce_product; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_product (
    id integer NOT NULL,
    name character varying(255),
    teaser text,
    description text,
    url text,
    priority integer DEFAULT 0 NOT NULL,
    publish integer DEFAULT 0 NOT NULL,
    other_data text,
    modified timestamp(0) without time zone DEFAULT now() NOT NULL,
    availability smallint DEFAULT 0 NOT NULL,
    name_aka character varying(255)
);


--
-- Name: ecommerce_product_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_product_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_product_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_product_id_seq OWNED BY ecommerce_product.id;


--
-- Name: ecommerce_product_image; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_product_image (
    id integer NOT NULL,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer,
    content text,
    other_data text,
    link_to_node_id integer,
    customer_id integer
);


--
-- Name: ecommerce_product_image_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_product_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_product_image_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_product_image_id_seq OWNED BY ecommerce_product_image.id;


--
-- Name: ecommerce_product_review; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_product_review (
    id integer NOT NULL,
    parent integer,
    node_id integer,
    title character varying(255),
    content text,
    author_name character varying(255),
    author_email character varying(255),
    author_website character varying(255),
    author_ip_address character varying(255),
    customer_id integer NOT NULL,
    created timestamp(0) without time zone DEFAULT now(),
    publish smallint,
    rating smallint DEFAULT 0,
    relation_subject text
);


--
-- Name: ecommerce_product_review_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_product_review_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_product_review_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_product_review_id_seq OWNED BY ecommerce_product_review.id;


--
-- Name: ecommerce_product_taxonomy; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_product_taxonomy (
    id integer NOT NULL,
    node_id integer NOT NULL,
    taxonomy_tree_id integer NOT NULL
);


--
-- Name: ecommerce_product_taxonomy_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_product_taxonomy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_product_taxonomy_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_product_taxonomy_id_seq OWNED BY ecommerce_product_taxonomy.id;


--
-- Name: ecommerce_product_to_product; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_product_to_product (
    id integer NOT NULL,
    product_id integer,
    related_product_id integer
);


--
-- Name: ecommerce_product_to_product_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_product_to_product_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_product_to_product_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_product_to_product_id_seq OWNED BY ecommerce_product_to_product.id;


--
-- Name: ecommerce_product_type; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_product_type (
    id integer NOT NULL,
    name character varying(255),
    vat numeric DEFAULT 0 NOT NULL,
    publish integer DEFAULT 1 NOT NULL
);


--
-- Name: ecommerce_product_type_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_product_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_product_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_product_type_id_seq OWNED BY ecommerce_product_type.id;


--
-- Name: ecommerce_product_variety; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_product_variety (
    id integer NOT NULL,
    name character varying(255),
    product_id integer,
    sku character varying(255),
    weight integer,
    weight_gross integer,
    stock integer,
    priority integer DEFAULT 0 NOT NULL,
    description text,
    other_data text,
    width integer DEFAULT 0 NOT NULL,
    height integer DEFAULT 0 NOT NULL,
    depth integer DEFAULT 0 NOT NULL,
    diameter integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone DEFAULT now() NOT NULL,
    publish smallint DEFAULT 0 NOT NULL,
    display_permission integer DEFAULT 0 NOT NULL,
    ean13 character varying(255),
    upc character varying(255),
    condition smallint DEFAULT 0 NOT NULL,
    wholesale smallint,
    reward_points integer,
    subtitle character varying(255),
    product_type_id integer,
    limit_to_delivery_zones character varying(512)
);


--
-- Name: ecommerce_product_variety_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_product_variety_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_product_variety_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_product_variety_id_seq OWNED BY ecommerce_product_variety.id;


--
-- Name: ecommerce_product_variety_image; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_product_variety_image (
    id integer NOT NULL,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer,
    content text,
    other_data text,
    link_to_node_id integer,
    customer_id integer
);


--
-- Name: ecommerce_product_variety_image_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_product_variety_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_product_variety_image_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_product_variety_image_id_seq OWNED BY ecommerce_product_variety_image.id;


--
-- Name: ecommerce_product_variety_taxonomy; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_product_variety_taxonomy (
    id integer NOT NULL,
    node_id integer NOT NULL,
    taxonomy_tree_id integer NOT NULL
);


--
-- Name: ecommerce_product_variety_taxonomy_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_product_variety_taxonomy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_product_variety_taxonomy_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_product_variety_taxonomy_id_seq OWNED BY ecommerce_product_variety_taxonomy.id;


--
-- Name: ecommerce_promotion; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_promotion (
    id integer NOT NULL,
    title character varying(255),
    description text,
    publish smallint DEFAULT 1 NOT NULL,
    created timestamp(0) without time zone DEFAULT now() NOT NULL,
    modified timestamp(0) without time zone DEFAULT now() NOT NULL,
    customer_account_type smallint DEFAULT 0 NOT NULL,
    code_pattern character varying(255) NOT NULL,
    discount_fixed_value numeric(12,5) DEFAULT 0 NOT NULL,
    discount_percentage_value numeric(5,2) DEFAULT 0 NOT NULL,
    discount_free_delivery smallint DEFAULT 0 NOT NULL,
    uses_per_coupon integer DEFAULT 0 NOT NULL,
    uses_per_customer smallint DEFAULT 0 NOT NULL,
    limit_list_products text,
    other_data text,
    limit_delivery_country_id smallint DEFAULT 0 NOT NULL,
    limit_delivery_carrier_id smallint DEFAULT 0 NOT NULL,
    generated_by_order_id integer,
    generated_by_customer_id integer,
    limit_by_customer_id integer DEFAULT 0,
    limit_to_first_order smallint DEFAULT 0 NOT NULL,
    limit_to_order_amount numeric(12,5) DEFAULT 0,
    type integer,
    limit_cumulative_discount numeric(12,5) DEFAULT 0,
    free_promo_products text
);


--
-- Name: ecommerce_promotion_code; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_promotion_code (
    id integer NOT NULL,
    promotion_id integer NOT NULL,
    code character varying(255),
    order_id integer NOT NULL
);


--
-- Name: ecommerce_promotion_code_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_promotion_code_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_promotion_code_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_promotion_code_id_seq OWNED BY ecommerce_promotion_code.id;


--
-- Name: ecommerce_promotion_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_promotion_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_promotion_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_promotion_id_seq OWNED BY ecommerce_promotion.id;


--
-- Name: ecommerce_promotion_type; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_promotion_type (
    id integer NOT NULL,
    title character varying(255),
    description text,
    taxable smallint DEFAULT 0 NOT NULL,
    publish smallint DEFAULT 1 NOT NULL,
    created timestamp(0) without time zone DEFAULT now() NOT NULL,
    modified timestamp(0) without time zone DEFAULT now() NOT NULL,
    other_data text
);


--
-- Name: ecommerce_promotion_type_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_promotion_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_promotion_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_promotion_type_id_seq OWNED BY ecommerce_promotion_type.id;


--
-- Name: ecommerce_recipe; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_recipe (
    id integer NOT NULL,
    title character varying(255),
    description text,
    instructions text,
    video_url text,
    serving_people integer,
    preparation_time integer,
    cooking_time integer,
    priority integer,
    created timestamp without time zone,
    modified timestamp without time zone DEFAULT now(),
    publish smallint,
    other_data text
);


--
-- Name: ecommerce_recipe_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_recipe_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_recipe_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_recipe_id_seq OWNED BY ecommerce_recipe.id;


--
-- Name: ecommerce_recipe_image; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_recipe_image (
    id integer NOT NULL,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone DEFAULT now(),
    author integer,
    content text,
    other_data text,
    link_to_node_id integer,
    customer_id integer
);


--
-- Name: ecommerce_recipe_image_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_recipe_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_recipe_image_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_recipe_image_id_seq OWNED BY ecommerce_recipe_image.id;


--
-- Name: ecommerce_recipe_ingredients; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_recipe_ingredients (
    id integer NOT NULL,
    recipe_id integer,
    product_variety_id integer NOT NULL,
    quantity real,
    units integer,
    notes text,
    group_title character varying(255)
);


--
-- Name: ecommerce_recipe_ingredients_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_recipe_ingredients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_recipe_ingredients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_recipe_ingredients_id_seq OWNED BY ecommerce_recipe_ingredients.id;


--
-- Name: ecommerce_recipe_review; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_recipe_review (
    id integer NOT NULL,
    parent integer,
    node_id integer,
    title character varying(255),
    content text,
    author_name character varying(255),
    author_email character varying(255),
    author_website character varying(255),
    author_ip_address character varying(255),
    customer_id integer NOT NULL,
    created timestamp(0) without time zone DEFAULT now(),
    publish smallint,
    rating smallint DEFAULT 0,
    relation_subject text
);


--
-- Name: ecommerce_recipe_review_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_recipe_review_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_recipe_review_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_recipe_review_id_seq OWNED BY ecommerce_recipe_review.id;


--
-- Name: ecommerce_recipe_taxonomy; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_recipe_taxonomy (
    id integer NOT NULL,
    node_id integer NOT NULL,
    taxonomy_tree_id integer NOT NULL
);


--
-- Name: ecommerce_recipe_taxonomy_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_recipe_taxonomy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_recipe_taxonomy_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_recipe_taxonomy_id_seq OWNED BY ecommerce_recipe_taxonomy.id;


--
-- Name: ecommerce_store; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_store (
    id integer NOT NULL,
    title character varying(255),
    description text,
    address text,
    opening_hours text,
    telephone character varying(255),
    manager_name character varying(255),
    email character varying(255),
    type_id integer,
    coordinates_x integer,
    coordinates_y integer,
    latitude double precision,
    longitude double precision,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    publish smallint DEFAULT 0 NOT NULL,
    street_view_options text,
    other_data text,
    country_id integer,
    address_name character varying(255),
    address_line_1 character varying(255),
    address_line_2 character varying(255),
    address_line_3 character varying(255),
    address_city character varying(255),
    address_county character varying(255),
    address_post_code character varying(255),
    code character varying(255),
    url character varying(512)
);


--
-- Name: ecommerce_store_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_store_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_store_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_store_id_seq OWNED BY ecommerce_store.id;


--
-- Name: ecommerce_store_image; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_store_image (
    id integer NOT NULL,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer,
    content text,
    other_data text,
    link_to_node_id integer,
    customer_id integer
);


--
-- Name: ecommerce_store_image_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_store_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_store_image_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_store_image_id_seq OWNED BY ecommerce_store_image.id;


--
-- Name: ecommerce_store_taxonomy; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_store_taxonomy (
    id integer NOT NULL,
    node_id integer NOT NULL,
    taxonomy_tree_id integer NOT NULL
);


--
-- Name: ecommerce_store_taxonomy_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_store_taxonomy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_store_taxonomy_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_store_taxonomy_id_seq OWNED BY ecommerce_store_taxonomy.id;


--
-- Name: ecommerce_store_type; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_store_type (
    id integer NOT NULL,
    title character varying(255),
    description text,
    publish smallint DEFAULT 1 NOT NULL,
    created timestamp(0) without time zone DEFAULT now() NOT NULL,
    modified timestamp(0) without time zone DEFAULT now() NOT NULL,
    other_data text
);


--
-- Name: ecommerce_store_type_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_store_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_store_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_store_type_id_seq OWNED BY ecommerce_store_type.id;


--
-- Name: ecommerce_transaction; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_transaction (
    id integer NOT NULL,
    order_id integer,
    pg_data text,
    currency_code character(3),
    amount numeric(12,5),
    created timestamp(0) without time zone,
    type character varying(255),
    status smallint
);


--
-- Name: ecommerce_transaction_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_transaction_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_transaction_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_transaction_id_seq OWNED BY ecommerce_transaction.id;


--
-- Name: education_survey; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE education_survey (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    description text,
    created timestamp(0) without time zone DEFAULT now() NOT NULL,
    modified timestamp(0) without time zone DEFAULT now(),
    priority smallint DEFAULT 0,
    publish smallint DEFAULT 0,
    other_data text
);


--
-- Name: education_survey_entry; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE education_survey_entry (
    id integer NOT NULL,
    survey_id integer NOT NULL,
    customer_id integer NOT NULL,
    relation_subject text,
    created timestamp(0) without time zone DEFAULT now() NOT NULL,
    modified timestamp(0) without time zone DEFAULT now(),
    publish smallint DEFAULT 0,
    ip_address character varying(255),
    session_id character varying(32),
    other_data text
);


--
-- Name: education_survey_entry_answer; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE education_survey_entry_answer (
    id integer NOT NULL,
    survey_entry_id integer NOT NULL,
    question_id integer NOT NULL,
    question_answer_id integer,
    value text,
    created timestamp(0) without time zone DEFAULT now() NOT NULL,
    modified timestamp(0) without time zone DEFAULT now(),
    publish smallint DEFAULT 0
);


--
-- Name: education_survey_entry_answer_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE education_survey_entry_answer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: education_survey_entry_answer_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE education_survey_entry_answer_id_seq OWNED BY education_survey_entry_answer.id;


--
-- Name: education_survey_entry_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE education_survey_entry_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: education_survey_entry_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE education_survey_entry_id_seq OWNED BY education_survey_entry.id;


--
-- Name: education_survey_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE education_survey_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: education_survey_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE education_survey_id_seq OWNED BY education_survey.id;


--
-- Name: education_survey_image; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE education_survey_image (
    id integer NOT NULL,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer,
    content text,
    other_data text,
    link_to_node_id integer,
    customer_id integer
);


--
-- Name: education_survey_image_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE education_survey_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: education_survey_image_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE education_survey_image_id_seq OWNED BY education_survey_image.id;


--
-- Name: education_survey_question; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE education_survey_question (
    id integer NOT NULL,
    survey_id integer NOT NULL,
    parent integer,
    step smallint DEFAULT 1,
    title character varying(255) NOT NULL,
    description text,
    mandatory smallint DEFAULT 1,
    type character varying(255) NOT NULL,
    priority smallint DEFAULT 0,
    publish smallint DEFAULT 1,
    weight real DEFAULT 1 NOT NULL,
    content text,
    other_data text
);


--
-- Name: education_survey_question_answer; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE education_survey_question_answer (
    id integer NOT NULL,
    question_id integer NOT NULL,
    title text NOT NULL,
    description text,
    is_correct smallint,
    points smallint,
    priority smallint DEFAULT 0,
    publish smallint DEFAULT 1,
    content text,
    other_data text
);


--
-- Name: education_survey_question_answer_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE education_survey_question_answer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: education_survey_question_answer_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE education_survey_question_answer_id_seq OWNED BY education_survey_question_answer.id;


--
-- Name: education_survey_question_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE education_survey_question_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: education_survey_question_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE education_survey_question_id_seq OWNED BY education_survey_question.id;


--
-- Name: international_country; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE international_country (
    id integer NOT NULL,
    name character varying(255),
    iso_code2 character(2),
    iso_code3 character(3),
    eu_status boolean,
    currency_code character(3),
    publish smallint
);


--
-- Name: international_country_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE international_country_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: international_country_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE international_country_id_seq OWNED BY international_country.id;


--
-- Name: international_currency; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE international_currency (
    id integer NOT NULL,
    code character(3),
    name character varying(255) DEFAULT ''::character varying NOT NULL,
    symbol_left character varying(255),
    symbol_right character varying(255)
);


--
-- Name: international_currency_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE international_currency_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: international_currency_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE international_currency_id_seq OWNED BY international_currency.id;


--
-- Name: international_currency_rate; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE international_currency_rate (
    id integer NOT NULL,
    currency_code character(3),
    currency_code_from character(3),
    source character varying(255),
    date timestamp(0) without time zone,
    amount numeric(12,8)
);


--
-- Name: international_currency_rate_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE international_currency_rate_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: international_currency_rate_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE international_currency_rate_id_seq OWNED BY international_currency_rate.id;


--
-- Name: international_translation; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE international_translation (
    id integer NOT NULL,
    locale character varying(20) NOT NULL,
    original_string text NOT NULL,
    translated_string text NOT NULL,
    context character varying(63),
    node_id integer
);


--
-- Name: international_translation_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE international_translation_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: international_translation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE international_translation_id_seq OWNED BY international_translation.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_action ALTER COLUMN id SET DEFAULT nextval('client_action_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_address ALTER COLUMN id SET DEFAULT nextval('client_address_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_company ALTER COLUMN id SET DEFAULT nextval('client_company_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_customer ALTER COLUMN id SET DEFAULT nextval('client_customer_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_customer_group ALTER COLUMN id SET DEFAULT nextval('client_customer_group_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_customer_image ALTER COLUMN id SET DEFAULT nextval('client_customer_image_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_customer_role ALTER COLUMN id SET DEFAULT nextval('client_customer_role_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_customer_taxonomy ALTER COLUMN id SET DEFAULT nextval('client_customer_taxonomy_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_customer_token ALTER COLUMN id SET DEFAULT nextval('client_customer_token_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_group ALTER COLUMN id SET DEFAULT nextval('client_group_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_role ALTER COLUMN id SET DEFAULT nextval('client_role_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_role_permission ALTER COLUMN id SET DEFAULT nextval('client_role_permission_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_comment ALTER COLUMN id SET DEFAULT nextval('common_comment_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_configuration ALTER COLUMN id SET DEFAULT nextval('common_configuration_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_email ALTER COLUMN id SET DEFAULT nextval('common_email_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_file ALTER COLUMN id SET DEFAULT nextval('common_file_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_image ALTER COLUMN id SET DEFAULT nextval('common_image_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_node ALTER COLUMN id SET DEFAULT nextval('common_node_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_node_taxonomy ALTER COLUMN id SET DEFAULT nextval('common_node_taxonomy_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_print_article ALTER COLUMN id SET DEFAULT nextval('common_print_article_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_revision ALTER COLUMN id SET DEFAULT nextval('common_revision_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_scheduler ALTER COLUMN id SET DEFAULT nextval('common_scheduler_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_session ALTER COLUMN id SET DEFAULT nextval('common_session_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_session_archive ALTER COLUMN id SET DEFAULT nextval('common_session_archive_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_taxonomy_label ALTER COLUMN id SET DEFAULT nextval('common_taxonomy_label_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_taxonomy_label_image ALTER COLUMN id SET DEFAULT nextval('common_taxonomy_label_image_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_taxonomy_tree ALTER COLUMN id SET DEFAULT nextval('common_taxonomy_tree_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_uri_mapping ALTER COLUMN id SET DEFAULT nextval('common_uri_mapping_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_watchdog ALTER COLUMN id SET DEFAULT nextval('common_watchdog_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_basket ALTER COLUMN id SET DEFAULT nextval('ecommerce_basket_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_basket_content ALTER COLUMN id SET DEFAULT nextval('ecommerce_basket_content_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_delivery ALTER COLUMN id SET DEFAULT nextval('ecommerce_delivery_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_delivery_carrier ALTER COLUMN id SET DEFAULT nextval('ecommerce_delivery_carrier_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_delivery_carrier_rate ALTER COLUMN id SET DEFAULT nextval('ecommerce_delivery_carrier_rate_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_delivery_carrier_zone ALTER COLUMN id SET DEFAULT nextval('ecommerce_delivery_carrier_zone_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_delivery_carrier_zone_to_country ALTER COLUMN id SET DEFAULT nextval('ecommerce_delivery_carrier_zone_to_country_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_invoice ALTER COLUMN id SET DEFAULT nextval('ecommerce_invoice_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_offer ALTER COLUMN id SET DEFAULT nextval('ecommerce_offer_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_offer_group ALTER COLUMN id SET DEFAULT nextval('ecommerce_offer_group_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_order ALTER COLUMN id SET DEFAULT nextval('ecommerce_order_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_order_log ALTER COLUMN id SET DEFAULT nextval('ecommerce_order_log_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_price ALTER COLUMN id SET DEFAULT nextval('ecommerce_price_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product ALTER COLUMN id SET DEFAULT nextval('ecommerce_product_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_image ALTER COLUMN id SET DEFAULT nextval('ecommerce_product_image_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_review ALTER COLUMN id SET DEFAULT nextval('ecommerce_product_review_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_taxonomy ALTER COLUMN id SET DEFAULT nextval('ecommerce_product_taxonomy_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_to_product ALTER COLUMN id SET DEFAULT nextval('ecommerce_product_to_product_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_type ALTER COLUMN id SET DEFAULT nextval('ecommerce_product_type_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_variety ALTER COLUMN id SET DEFAULT nextval('ecommerce_product_variety_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_variety_image ALTER COLUMN id SET DEFAULT nextval('ecommerce_product_variety_image_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_variety_taxonomy ALTER COLUMN id SET DEFAULT nextval('ecommerce_product_variety_taxonomy_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_promotion ALTER COLUMN id SET DEFAULT nextval('ecommerce_promotion_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_promotion_code ALTER COLUMN id SET DEFAULT nextval('ecommerce_promotion_code_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_promotion_type ALTER COLUMN id SET DEFAULT nextval('ecommerce_promotion_type_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_recipe ALTER COLUMN id SET DEFAULT nextval('ecommerce_recipe_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_recipe_image ALTER COLUMN id SET DEFAULT nextval('ecommerce_recipe_image_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_recipe_ingredients ALTER COLUMN id SET DEFAULT nextval('ecommerce_recipe_ingredients_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_recipe_review ALTER COLUMN id SET DEFAULT nextval('ecommerce_recipe_review_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_recipe_taxonomy ALTER COLUMN id SET DEFAULT nextval('ecommerce_recipe_taxonomy_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_store ALTER COLUMN id SET DEFAULT nextval('ecommerce_store_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_store_image ALTER COLUMN id SET DEFAULT nextval('ecommerce_store_image_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_store_taxonomy ALTER COLUMN id SET DEFAULT nextval('ecommerce_store_taxonomy_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_store_type ALTER COLUMN id SET DEFAULT nextval('ecommerce_store_type_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_transaction ALTER COLUMN id SET DEFAULT nextval('ecommerce_transaction_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY education_survey ALTER COLUMN id SET DEFAULT nextval('education_survey_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY education_survey_entry ALTER COLUMN id SET DEFAULT nextval('education_survey_entry_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY education_survey_entry_answer ALTER COLUMN id SET DEFAULT nextval('education_survey_entry_answer_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY education_survey_image ALTER COLUMN id SET DEFAULT nextval('education_survey_image_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY education_survey_question ALTER COLUMN id SET DEFAULT nextval('education_survey_question_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY education_survey_question_answer ALTER COLUMN id SET DEFAULT nextval('education_survey_question_answer_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY international_country ALTER COLUMN id SET DEFAULT nextval('international_country_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY international_currency ALTER COLUMN id SET DEFAULT nextval('international_currency_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY international_currency_rate ALTER COLUMN id SET DEFAULT nextval('international_currency_rate_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY international_translation ALTER COLUMN id SET DEFAULT nextval('international_translation_id_seq'::regclass);


--
-- Data for Name: client_action; Type: TABLE DATA; Schema: public; Owner: -
--

COPY client_action (id, customer_id, node_id, action_id, network, action_name, object_name, created, modified, other_data) FROM stdin;
\.


--
-- Name: client_action_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('client_action_id_seq', 1, false);


--
-- Data for Name: client_address; Type: TABLE DATA; Schema: public; Owner: -
--

COPY client_address (id, customer_id, country_id, name, line_1, line_2, line_3, post_code, city, county, telephone, comment, is_deleted) FROM stdin;
1	1	222	Mr Onxshop Tester	58 Howard Street			BT1 6PJ	Belfast	\N	\N	\N	\N
\.


--
-- Name: client_address_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('client_address_id_seq', 1, true);


--
-- Data for Name: client_company; Type: TABLE DATA; Schema: public; Owner: -
--

COPY client_company (id, name, www, telephone, fax, customer_id, registration_no, vat_no, other_data) FROM stdin;
\.


--
-- Name: client_company_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('client_company_id_seq', 1, false);


--
-- Data for Name: client_customer; Type: TABLE DATA; Schema: public; Owner: -
--

COPY client_customer (id, title_before, first_name, last_name, title_after, email, username, telephone, mobilephone, nickname, password, company_id, invoices_address_id, delivery_address_id, gender, created, currency_code, status, newsletter, birthday, other_data, modified, account_type, agreed_with_latest_t_and_c, verified_email_address, oauth, deleted_date, facebook_id, twitter_id, google_id, profile_image_url, store_id, janrain_id) FROM stdin;
0		Anonym	Anonymous		anonym@noemail.noemail	anonymous	notelephone			9ce21d8f3992d89a325aa9dcf520a591	0	1	1	 	2011-12-13 14:00:00	GBP	0	0	2007-06-14		2011-12-13 14:00:00	0	0	0	\N	\N	\N	\N	\N	\N	\N	\N
1	Ing.	Onxshop	Tester	\N	test@onxshop.com	\N	+44(0) 2890 328 988	\N	\N	b3f61bf1cb26243ef478a3c181dd0aa2	0	1	1	\N	2011-12-13 14:00:00	CZK	1	0	\N		2011-12-13 14:00:00	0	0	0	\N	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: client_customer_group; Type: TABLE DATA; Schema: public; Owner: -
--

COPY client_customer_group (id, group_id, customer_id, created, modified) FROM stdin;
\.


--
-- Name: client_customer_group_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('client_customer_group_id_seq', 1, false);


--
-- Name: client_customer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('client_customer_id_seq', 1, true);


--
-- Data for Name: client_customer_image; Type: TABLE DATA; Schema: public; Owner: -
--

COPY client_customer_image (id, src, role, node_id, title, description, priority, modified, author, content, other_data, link_to_node_id, customer_id) FROM stdin;
\.


--
-- Name: client_customer_image_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('client_customer_image_id_seq', 1, false);


--
-- Data for Name: client_customer_role; Type: TABLE DATA; Schema: public; Owner: -
--

COPY client_customer_role (id, role_id, customer_id, created, modified) FROM stdin;
\.


--
-- Name: client_customer_role_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('client_customer_role_id_seq', 1, false);


--
-- Data for Name: client_customer_taxonomy; Type: TABLE DATA; Schema: public; Owner: -
--

COPY client_customer_taxonomy (id, node_id, taxonomy_tree_id) FROM stdin;
\.


--
-- Name: client_customer_taxonomy_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('client_customer_taxonomy_id_seq', 1, false);


--
-- Data for Name: client_customer_token; Type: TABLE DATA; Schema: public; Owner: -
--

COPY client_customer_token (id, customer_id, publish, token, oauth_data, other_data, ttl, ip_address, http_user_agent, created, modified) FROM stdin;
\.


--
-- Name: client_customer_token_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('client_customer_token_id_seq', 1, false);


--
-- Data for Name: client_group; Type: TABLE DATA; Schema: public; Owner: -
--

COPY client_group (id, name, description, search_filter, other_data) FROM stdin;
\.


--
-- Name: client_group_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('client_group_id_seq', 1, false);


--
-- Data for Name: client_role; Type: TABLE DATA; Schema: public; Owner: -
--

COPY client_role (id, name, description, other_data) FROM stdin;
1	Admin	\N	\N
2	Front Office Only CMS Editor	\N	\N
3	CMS Editor	\N	\N
4	eCommerce Editor	\N	\N
5	Customer Services	\N	\N
6	Warehouse	\N	\N
\.


--
-- Name: client_role_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('client_role_id_seq', 1000, true);


--
-- Data for Name: client_role_permission; Type: TABLE DATA; Schema: public; Owner: -
--

COPY client_role_permission (id, role_id, resource, operation, scope, created, modified, other_data) FROM stdin;
1	1	_all_	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
2	2	front_office	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
3	2	nodes	edit	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
4	2	media	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
5	3	front_office	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
6	3	back_office	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
7	3	nodes	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
8	3	comments	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
9	3	surveys	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
10	3	media	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
11	3	taxonomy	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
12	3	seo_manager	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
13	3	scheduler	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
14	3	search_index	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
15	4	products	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
16	4	recipes	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
17	4	stores	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
18	4	orders	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
19	4	stock	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
20	4	customers	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
21	4	reports	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
22	4	discounts	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
23	4	currency	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
24	5	back_office	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
25	5	customers	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
26	5	orders	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
27	5	comments	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
28	5	surveys	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
29	5	discounts	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
30	6	stock	_all_	\N	2014-12-31 20:37:04.686436	2014-12-31 20:37:04.686436	\N
\.


--
-- Name: client_role_permission_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('client_role_permission_id_seq', 30, true);


--
-- Data for Name: common_comment; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_comment (id, parent, node_id, title, content, author_name, author_email, author_website, author_ip_address, customer_id, created, publish, rating, relation_subject) FROM stdin;
0	\N	0	Base	n/a	n/a	noemail@noemail.com	n/a	n/a	0	2008-08-06 21:25:04	0	0	\N
\.


--
-- Name: common_comment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_comment_id_seq', 1, false);


--
-- Data for Name: common_configuration; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_configuration (id, node_id, object, property, value, description, apply_to_children) FROM stdin;
5	0	global	locale	cs_CZ.UTF-8		0
6	0	global	default_currency	CZK		0
1	0	global	title	Prázdný web		0
2	0	global	author_content	Prázdný web, http://www.vaseadresa.cz/		0
9	0	global	google_analytics			0
8	0	global	css	/**\r\n *\r\n * Our hint to CSS developers: \r\n * use here an @import of a CSS file from your own server,\r\n * work on your local version and paste here the final version \r\n * when you are finished with the development\r\n *\r\n */\r\n \r\n@import url(/share/css/default/theme_colour/grey.css);\r\n/*@import url(/share/css/default/theme_layout/stripes.css);*/\r\n		0
4	0	global	html_title_suffix	- Prázdný web		0
10	0	global	google_adwords			0
11	0	global	display_content_side	1		0
12	0	global	extra_head	<meta name="viewport" content="width=1024" />		0
13	0	global	extra_body_top			0
14	0	global	extra_body_bottom			0
15	0	global	display_secondary_navigation	0		0
16	0	global	display_content_foot	0		0
17	5	global	html_title_suffix			0
7	0	global	admin_email	test@onxshop.com		0
3	0	global	credit	<a href="http://onxshop.com" title="Easy web CMS/eCommerce"><span>Powered by Onxshop</span></a>		0
\.


--
-- Name: common_configuration_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_configuration_id_seq', 17, true);


--
-- Data for Name: common_email; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_email (id, email_from, name_from, subject, content, template, email_recipient, name_recipient, created, ip) FROM stdin;
\.


--
-- Name: common_email_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_email_id_seq', 1, false);


--
-- Data for Name: common_file; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_file (id, src, role, node_id, title, description, priority, modified, author, content, other_data, link_to_node_id, customer_id) FROM stdin;
\.


--
-- Name: common_file_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_file_id_seq', 1, false);


--
-- Data for Name: common_image; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_image (id, src, role, node_id, title, description, priority, modified, author, content, other_data, link_to_node_id, customer_id) FROM stdin;
\.


--
-- Name: common_image_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_image_id_seq', 1, true);


--
-- Data for Name: common_node; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_node (id, title, node_group, node_controller, parent, parent_container, priority, strapline, content, description, keywords, page_title, head, created, modified, publish, display_in_menu, author, uri_title, display_permission, other_data, css_class, layout_style, component, relations, display_title, display_secondary_navigation, require_login, display_breadcrumb, browser_title, link_to_node_id, require_ssl, display_permission_group_acl, share_counter, customer_id, custom_fields) FROM stdin;
89	Select Delivery Method	content	component	7	1	100	\N	\N					2010-04-18 01:34:49	2010-04-18 11:10:57	1	1	1000		0	N;			a:3:{s:8:"template";s:30:"ecommerce/delivery_option.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	1	\N	\N	0		0	0	\N	0	\N	\N
91	Newsletter Subscribe	content	component	90	1	0	\N	\N					2010-04-18 11:20:58	2010-04-18 11:21:14	1	1	1000		0	N;			a:3:{s:8:"template";s:32:"client/newsletter_subscribe.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
69	Search result	content	component	21	1	0	\N				\N		2006-09-30 15:49:27	2008-08-07 01:21:51	1	1	1000		0	N;			a:3:{s:8:"template";s:17:"search_nodes.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
72	Sitemap component	content	component	22	1	0	\N				\N		2006-09-30 15:50:21	2008-08-24 00:51:29	1	1	1000		0	N;			a:3:{s:8:"template";s:12:"sitemap.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
1016	Privacy Policy	content	rte	26	1	0	\N	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\r\n<ul>\r\n<li>velit esse cillum dolore</li>\r\n<li>consectetur adipisicing elit</li>\r\n<li>occaecat cupidatat non proident</li>\r\n</ul>\r\n<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>			\N		2008-08-16 13:00:53	2008-08-16 13:01:11	1	1	1000		0	N;			N;	N;	1	\N	\N	0		0	0	\N	0	\N	\N
1017	Returns policy	content	rte	26	2	0	\N	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\r\n<ul>\r\n<li>velit esse cillum dolore</li>\r\n<li>consectetur adipisicing elit</li>\r\n<li>occaecat cupidatat non proident</li>\r\n</ul>\r\n<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>			\N		2008-08-16 13:01:53	2008-08-16 13:01:58	1	1	1000		0	N;			N;	N;	1	\N	\N	0		0	0	\N	0	\N	\N
68	Search input	content	component	21	1	0	\N				\N		2006-09-30 15:48:45	2008-08-24 18:22:11	1	1	1000		0	N;			a:3:{s:8:"template";s:14:"searchbox.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
75	Basket edit component	content	component	6	1	0	\N				\N		2006-09-30 15:54:35	2008-08-24 18:23:16	1	1	1000		0	N;			a:3:{s:8:"template";s:26:"ecommerce/basket_edit.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
41	Checkout	content	component	7	1	0	\N				\N		2006-09-30 14:47:01	2008-08-24 18:23:33	1	1	1000		0	N;			a:3:{s:8:"template";s:23:"ecommerce/checkout.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
39	Checkout Basket	content	component	7	1	0	\N				\N		2006-09-30 14:44:34	2008-08-24 18:23:51	1	1	1000		0	N;			a:3:{s:8:"template";s:30:"ecommerce/checkout_basket.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
51	Order detail component	content	component	19	1	0	\N				\N		2006-09-30 15:22:49	2008-08-24 18:25:32	1	1	1000		0	N;			a:3:{s:8:"template";s:27:"ecommerce/order_detail.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
60	Payment component	content	component	10	1	0	\N				\N		2006-09-30 15:32:26	2008-08-24 18:26:22	1	1	1000		0	N;			a:3:{s:8:"template";s:22:"ecommerce/payment.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
65	Payment was successfull	content	rte	12	1	0	\N	<p>Process executed without error and the transaction was successfully Authorised.&nbsp;</p>			\N		2006-09-30 15:43:50	2008-08-24 18:27:47	1	1	1000		0	N;			N;	N;	0	\N	\N	0		0	0	\N	0	\N	\N
78	404 error	content	rte	14	1	0	\N	<p><strong>We have recently restructured this website, you might find what you are looking for by going via the <a href="/">home page</a>.</strong></p>\r\n<p><strong>If you believe you have found a broken link please <a href="/page/20">let us know</a>.</strong></p>\r\n<div class="line">\r\n<hr />\r\n</div>\r\n<p><strong>Please try the following:</strong></p>\r\n<ul>\r\n<li>If you typed the page address in the Address bar, make sure that it is spelled correctly. </li>\r\n<li>Click the <a href="javascript:history.go(-1)">Back</a> button to try another link. </li>\r\n</ul>\r\n<p>HTTP 404 : Page not found</p>			\N		2006-09-30 16:37:05	2008-08-24 18:28:28	1	1	1000		0	N;			N;	N;	1	\N	\N	0		0	0	\N	0	\N	\N
93	Newsletter Unsubscribe	content	component	92	1	0	\N	\N					2010-04-18 11:22:40	2010-04-18 11:22:56	1	1	1000		0	N;			a:3:{s:8:"template";s:34:"client/newsletter_unsubscribe.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
42	Address component	content	component	7	2	0	\N				\N		2006-09-30 14:54:43	2008-08-24 18:24:18	1	1	1000		0	N;			a:3:{s:8:"template";s:19:"client/address.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
1024	Userbox	content	component	15	2	0	\N	\N					2010-04-18 13:45:43	2010-04-18 13:46:15	1	1	1000		0	N;			a:3:{s:8:"template";s:19:"client/userbox.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
1015	Our latest news	content	news_list	83	1	0	\N	\N			\N		2008-08-16 04:02:19	2011-01-16 17:32:22	1	1	1000		0	N;			a:5:{s:5:"limit";s:1:"5";s:8:"template";s:4:"full";s:10:"pagination";i:1;s:5:"image";i:0;s:13:"display_title";i:0;}	N;	0	\N	0	0		0	0	\N	0	\N	\N
87	General content 2	content	rte	85	0	0	\N	<p style="text-align: center;"><em>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</em></p>			\N		2006-09-30 15:50:10	2011-01-16 17:36:38	1	1	1000		0	N;			N;	N;	0	\N	0	0		0	0	\N	0	\N	\N
1019	forgotten password	content	rte	8	1	0	\N	<p>\n<a href="/page/9">Zapomněli jste heslo od sv&eacute;ho &uacute;čtu?</a>  \n</p>			\N		2008-10-12 22:53:50	2008-10-12 22:58:49	1	1	1000		0	N;			N;	N;	0	\N	\N	0		0	0	\N	0	\N	\N
1020	Payment information	content	rte	8	2	0	\N	<h3>Platebn&iacute; karty<br /></h3>\n<p>Akceptujeme tyto platebn&iacute; karty: \n</p>\n<p>\n<img src="https://www.worldpay.com/cgenerator/logos/visa.gif" alt="Visa payments supported by WorldPay" />\n<img src="https://www.worldpay.com/cgenerator/logos/visa_delta.gif" alt="Visa/Delta payments supported by WorldPay" />\n<img src="https://www.worldpay.com/cgenerator/logos/mastercard.gif" alt="Mastercard payments supported by WorldPay" />\n<img src="https://www.worldpay.com/cgenerator/logos/switch.gif" alt="Switch payments supported by WorldPay" />\n</p>\n<h3>Obchodn&iacute; podm&iacute;nky<br /></h3>\n<p>Odesl&aacute;n&iacute;m objedn&aacute;vky přes tento web vyjadřujete souhlas s n&aacute;sleduj&iacute;c&iacute;mi <a href="/page/26">obchodn&iacute;mi podm&iacute;nkami</a><a href="/page/26"></a> .\n</p>\n<h3>Platebn&iacute; br&aacute;nu zaji&scaron;ťuje </h3>\n<p>\n<!-- Powered by WorldPay logo-->\n<a href="http://www.worldpay.com/"><img src="https://www.worldpay.com/cgenerator/logos/poweredByWorldPay.gif" alt="Powered By WorldPay" /></a>\n</p>\n<p>\n<!-- WorldPay Guarantee Logo -->\n<img src="https://www.worldpay.com/cgenerator/logos/guaranteed.gif" alt="WorldPay Guarantee" />\n</p>			\N		2008-10-12 23:03:43	2008-10-12 23:10:01	1	1	1000		0	N;			N;	N;	0	\N	\N	0		0	0	\N	0	\N	\N
1022	Related products	content	component	6	2	0	\N	\N			\N		2008-10-12 23:16:47	2008-10-12 23:17:54	1	1	1000		0	N;			a:3:{s:8:"template";s:37:"ecommerce/product_related_basket.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
1021	Recently viewed	content	component	6	1	0	\N	\N			\N		2008-10-12 23:15:43	2008-10-12 23:18:32	1	1	1000		0	N;			a:3:{s:8:"template";s:39:"ecommerce/recently_viewed_products.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
1023	content 1242392858	content	rte	5	1	0	\N	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\n<ul>\n<li>velit esse cillum dolore</li>\n<li>consectetur adipisicing elit</li>\n<li>occaecat cupidatat non proident</li>\n</ul>\n<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>					2009-05-15 14:07:38	2009-05-15 14:07:44	1	1	1000		0	N;			N;	N;	0	\N	\N	0		0	0	\N	0	\N	\N
45	Address Management Component	content	component	16	1	0	\N				\N		2006-09-30 15:20:05	2008-08-24 18:25:00	1	1	1000		0	N;			a:3:{s:8:"template";s:24:"client/address_edit.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
32	Existing customer	content	component	8	1	0	\N				\N		2006-09-30 14:00:05	2008-08-24 01:15:22	1	1	1000		0	N;			a:3:{s:8:"template";s:17:"client/login.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
36	Registration component	content	component	13	1	0	\N				\N		2006-09-30 14:26:09	2008-08-24 01:14:57	1	1	1000		0	N;			a:3:{s:8:"template";s:24:"client/registration.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
57	Password reset component	content	component	9	1	0	\N				\N		2006-09-30 15:30:31	2008-08-24 18:26:03	1	1	1000		0	N;			a:3:{s:8:"template";s:26:"client/password_reset.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
34	New customer	content	component	8	1	0	\N				\N		2006-09-30 14:01:50	2008-08-24 01:15:34	1	1	1000		0	N;			a:3:{s:8:"template";s:30:"client/registration_start.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
48	Your orders with us	content	component	17	1	0	\N				\N		2006-09-30 15:21:35	2008-08-16 13:22:33	1	1	1000		0	N;			a:3:{s:8:"template";s:25:"ecommerce/order_list.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	1	\N	\N	0		0	0	\N	0	\N	\N
63	Payment failure component	content	component	11	1	0	\N				\N		2006-09-30 15:42:05	2008-08-24 18:26:38	1	1	1000		0	N;			a:3:{s:8:"template";s:37:"ecommerce/payment/protx_callback.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
66	Payment success component	content	component	12	1	0	\N				\N		2006-09-30 15:44:42	2008-08-16 13:28:47	1	1	1000		0	N;			a:3:{s:8:"template";s:37:"ecommerce/payment/protx_callback.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	1	\N	\N	0		0	0	\N	0	\N	\N
0	Root	site	default	\N	0	0							2008-08-06 21:24:09	2008-08-06 21:24:09	1	1	0		0				\N	\N	\N	\N	\N	0		0	0	\N	0	\N	\N
1011	Naše adresa	content	rte	20	2	5	\N	<p>Jm&eacute;no Přijmen&iacute;<br />Ulice, č.p. xxx<br />PSČ Město<br />Kraj</p>\n<p>telefon: xxx xxx xxx</p>					2008-08-07 01:18:33	2011-01-16 17:31:49	1	1	1000		0	N;			N;	N;	1	\N	\N	0		0	0	\N	0	\N	\N
76	Zaslat zprávu	content	contact_form	20	1	15	\N						2006-09-30 16:00:21	2011-01-16 17:31:47	1	1	1000		0	N;			a:6:{s:7:"mail_to";s:0:"";s:11:"mail_toname";s:0:"";s:15:"node_controller";s:13:"common_simple";s:14:"sending_failed";s:84:"Musíte vypňit všechny požadované údaje, které jsou označeny hvězdičkou (*)";s:4:"text";s:27:"Děkujeme za Vaši zprávu.";s:4:"href";s:0:"";}	N;	1	\N	\N	0		0	0	\N	0	\N	\N
1029	content 1295195343	content	rte	1025	1	0	\N	<h3>Ochrana osobn&iacute;ch &uacute;dajů</h3>\n<p>Tyto podm&iacute;nky ochrany osobn&iacute;ch &uacute;dajů stanov&iacute;, jak&yacute;m způsobem [COMPANY NAME] použ&iacute;v&aacute; a chr&aacute;n&iacute; informace, kter&eacute; můžete za určit&yacute;ch okolnost&iacute; poskytnout při použ&iacute;v&aacute;n&iacute; str&aacute;nek um&iacute;stěn&yacute;ch na dom&eacute;ně [COMPANY DOMAIN]. </p>\n<p>[COMPANY NAME] V&aacute;m zaručuje plnou ochranu osobn&iacute;ch &uacute;dajů poskytovan&yacute;ch při použ&iacute;v&aacute;n&iacute; těchto internetov&yacute;ch str&aacute;nek. Pokud V&aacute;s pož&aacute;d&aacute;me o poskytnut&iacute; určit&yacute;ch informac&iacute;, kter&eacute; mohou sloužit k Va&scaron;&iacute; identifikaci při použit&iacute; těchto str&aacute;nek, zaručujeme, že tyto informace budou použity v&yacute;hradně v souladu s touto kodifikac&iacute; ochrany osobn&iacute;ch &uacute;dajů.</p>\n<p>[COMPANY NAME] může v budoucnu změnit tuto definici ochrany osobn&iacute;ch &uacute;dajů prostřednictv&iacute;m updatu těchto str&aacute;nek. Uživatel&eacute; by proto měli př&iacute;ležitostně zkontrolovat možn&eacute; změny a ujistit se, že souhlas&iacute; s aktu&aacute;ln&iacute; verzi podm&iacute;nek už&iacute;v&aacute;n&iacute; a ochrany osobn&iacute;ch &uacute;dajů. Současn&aacute; verze podm&iacute;nek už&iacute;v&aacute;n&iacute; a ochrany osobn&iacute;ch &uacute;dajů je platn&aacute; od [DATE]. </p>\n<h3>Osobn&iacute; &uacute;daje</h3>\n<p>Při použ&iacute;v&aacute;n&iacute; těchto str&aacute;nek můžete b&yacute;t pož&aacute;d&aacute;n&iacute; o poskytnut&iacute; n&aacute;sleduj&iacute;c&iacute;ch informac&iacute;:</p>\n<ul>\n<li>\n<p style="margin-bottom: 0cm;">jm&eacute;no a zaměstn&aacute;n&iacute;</p>\n</li>\n<li>\n<p style="margin-bottom: 0cm;">kontaktn&iacute; informace včetně\te-mailov&eacute; adresy</p>\n</li>\n<li>\n<p style="margin-bottom: 0cm;">demografick&eacute; informace jako je\tPSČ, oblasti z&aacute;jmu</p>\n</li>\n<li>\n<p style="margin-bottom: 0cm;">dal&scaron;&iacute; informace souvisej&iacute;c&iacute; s\tprůzkumem klientů či nab&iacute;dkami služeb a produktů</p>\n</li>\n</ul>\n<h3>Možnosti využit&iacute; osobn&iacute;ch dat</h3>\n<p>Při použ&iacute;v&aacute;n&iacute; na&scaron;ich webov&yacute;ch str&aacute;nek můžeme požadovat někter&eacute; informace, abychom l&eacute;pe porozuměli Va&scaron;im potřeb&aacute;m a poskytovali lep&scaron;&iacute; služby. Tyto informace mohou b&yacute;t vyžadov&aacute;ny zejm&eacute;na pro n&aacute;sleduj&iacute;c&iacute; &uacute;čely:</p>\n<ul>\n<li>\n<p>vnitřn&iacute; &uacute;četnictv&iacute; firmy</p>\n</li>\n<li>\n<p>zlep&scaron;en&iacute; na&scaron;ich služeb a nab&iacute;zen&yacute;ch produktů</p>\n</li>\n<li>\n<p>př&iacute;ležitostn&eacute; informačn&iacute; e-maily o\tnov&yacute;ch produktech, speci&aacute;ln&iacute;ch nab&iacute;dk&aacute;ch a dal&scaron;&iacute;ch t&eacute;matech,\to kter&yacute;ch se domn&iacute;v&aacute;me, že by pro V&aacute;s mohly b&yacute;t zaj&iacute;mav&eacute;</p>\n</li>\n<li>\n<p>osloven&iacute; uživatelů z důvodu průzkumu trhu, a to\tprostřednictv&iacute;m e-mailu či telefonu</p>\n</li>\n</ul>\n<h3>Bezpečnost</h3>\n<p>Zaručujeme, že se v&scaron;emi poskytovan&yacute;mi informacemi je zach&aacute;zeno v souladu s bezpečnostn&iacute;mi standardy a př&iacute;slu&scaron;n&yacute;mi pr&aacute;vn&iacute;mi předpisy. Abychom zabr&aacute;nili zneužit&iacute; či neautorizovan&eacute;mu použit&iacute; poskytnut&yacute;ch dat, uplatňujeme vhodn&aacute; fyzick&aacute;, elektronick&aacute; i manažersk&aacute; opatřen&iacute;, abychom ochr&aacute;nili data z&iacute;skan&aacute; online porstřednictv&iacute;m těchto str&aacute;nek.</p>\n<h3>Odkazy na dal&scaron;&iacute; str&aacute;nky</h3>\n<p>Na&scaron;e str&aacute;nky mohou obsahovat odkazy na str&aacute;nky třet&iacute;ch stran. Pokud použijete někter&yacute; z těchto odkazů a opust&iacute;te na&scaron;e str&aacute;nky, měli byste vz&iacute;t na vědom&iacute;, že nem&aacute;me ž&aacute;dnou kontrolu nad obsahem odkazovan&yacute;ch str&aacute;nek. Proto nejsme zodpovědn&iacute; za ochranu Va&scaron;ich osobn&iacute;ch &uacute;dajů, kter&eacute; poskytnete při použ&iacute;v&aacute;n&iacute; odkazovan&yacute;ch str&aacute;nek. Odkazovan&eacute; str&aacute;nky nejsou v&aacute;z&aacute;ny těmito pravidly pro ochranu osobn&iacute;ch &uacute;dajů. Proto byste měli b&yacute;t při poskytov&aacute;n&iacute; osobn&iacute;ch &uacute;dajů opatrn&iacute; a zkontrolovat pravidla pro ochranu uživatelů a jejich osobn&iacute;ch &uacute;dajů, vztahuj&iacute;c&iacute; se k př&iacute;slu&scaron;n&yacute;m  str&aacute;nk&aacute;m.</p>\n<h3>Kontrola Va&scaron;ich osobn&iacute;ch informac&iacute;</h3>\n<p>Zavazujeme se, že neposkytneme z&iacute;skan&eacute; osobn&iacute; informace  třet&iacute;m stran&aacute;m, a to ž&aacute;dn&yacute;m způsobem, za &uacute;platu ani bezplatně, bez Va&scaron;eho v&yacute;slovn&eacute;ho svolen&iacute;, př&iacute;padně pokud to nebudou vyžadovat pr&aacute;vn&iacute; předpisy. Můžeme využ&iacute;t Va&scaron;e osobn&iacute; informace k zasl&aacute;n&iacute; komerčn&iacute;ch informac&iacute; třet&iacute;ch stran, o kter&yacute;ch se domn&iacute;v&aacute;me, že by pro V&aacute;s mohly b&yacute;t zaj&iacute;mav&eacute;, pokud n&aacute;s o to pož&aacute;d&aacute;te.</p>\n<p>Pokud se domn&iacute;v&aacute;te, že jsou někter&eacute; dř&iacute;ve poskytnut&eacute; osobn&iacute; informace nespr&aacute;vn&eacute; či nekompletn&iacute;, informujte n&aacute;s pros&iacute;m e-mailem na adresu [COMPANY EMAIL]. </p>			\N		2011-01-16 17:29:03	2011-01-16 17:30:38	1	1	1000		0	N;			N;	N;	0	\N	0	0		0	0	\N	0	\N	\N
86	General content 1	content	rte	85	0	0	\N	<p>Jm&eacute;no,<br />Ulice č.p.<br />PSČ Město&nbsp;</p>			\N		2006-09-30 15:50:10	2011-01-16 17:31:29	1	1	1000		0	N;			N;	N;	0	\N	0	0		0	0	\N	0	\N	\N
1030	Archive	content	component	83	2	0	\N	\N			\N		2011-01-16 17:32:36	2011-01-16 17:32:56	1	1	1000		0	N;			a:3:{s:8:"template";s:17:"news_archive.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	1	\N	0	0		0	0	\N	0	\N	\N
90	Newsletter	page	default	4	0	0	\N	\N	\N	\N	\N	\N	2010-04-18 11:19:18	2010-04-18 11:19:18	1	0	1000	\N	0	\N		fibonacci-2-1	\N	\N	1	\N	\N	0		0	0	\N	0	\N	\N
84	Articles	page	default	3	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 12:07:59	2006-09-30 12:07:59	1	1	1000	\N	0	\N			\N	\N	\N	\N	\N	0		0	0	\N	0	\N	\N
92	Unsubscribe	page	default	90	0	0	\N	\N	\N	\N	\N	\N	2010-04-18 11:21:40	2010-04-18 11:21:40	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	1	\N	\N	0		0	0	\N	0	\N	\N
2	Commerce	container	default	0	0	0	\N	\N			\N		2006-09-30 09:55:17	2008-08-24 22:56:24	1	0	1000		0	N;			N;	N;	1	\N	\N	0		0	0	\N	0	\N	\N
3	Special	container	default	0	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 09:55:36	2006-09-30 09:55:36	1	0	1000	\N	0	\N			\N	\N	\N	\N	\N	0		0	0	\N	0	\N	\N
16	Správa adres	page	default	15	0	0		\N					2006-09-30 12:03:13	2008-08-24 22:35:52	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	1	0		0	1	\N	0	\N	\N
18	Osobní údaje	page	default	15	0	0		\N					2006-09-30 12:03:45	2008-08-24 22:36:24	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	1	0		0	1	\N	0	\N	\N
10	Platba	page	default	2	0	0		\N					2006-09-30 10:35:29	2008-08-24 22:36:51	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	1	0		0	1	\N	0	\N	\N
11	Selhání platby	page	default	2	0	0		\N					2006-09-30 10:35:43	2008-08-24 22:37:06	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	1	0		0	1	\N	0	\N	\N
12	Platba proběhla	page	default	2	0	0		\N					2006-09-30 10:35:59	2008-08-24 22:37:38	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	1	0		0	1	\N	0	\N	\N
7	Provedení objednávky	page	default	2	0	0		\N					2006-09-30 10:34:54	2008-08-24 22:38:56	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	1	0		0	1	\N	0	\N	\N
85	Content bits	container	default	3	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 12:07:59	2006-09-30 12:07:59	1	1	1000	\N	0	\N			\N	\N	\N	\N	\N	0		0	0	\N	0	\N	\N
26	Obchodní podmínky	page	default	4	0	0		N;					2006-09-30 13:40:50	2008-08-24 22:34:47	1	1	1000		0	N;		fibonacci-1-1	N;	N;	1	0	\N	0		0	0	\N	0	\N	\N
21	Vyhledat	page	default	4	0	0		\N					2006-09-30 12:08:07	2009-05-15 13:47:11	1	0	1000		0	N;		fibonacci-2-1	N;	N;	1	0	\N	0		0	0	\N	0	\N	\N
14	404	page	default	3	0	0		\N					2006-09-30 11:56:37	2008-08-16 13:06:19	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	\N	0		0	0	\N	0	\N	\N
22	Mapa stránek	page	default	4	0	0		\N					2006-09-30 12:08:21	2008-08-24 22:33:07	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	\N	0		0	0	\N	0	\N	\N
1	Primary navigation	container	default	0	0	10		N;			\N		2006-09-29 18:20:29	2011-01-16 17:25:09	1	1	1000		0	N;			N;	N;	1	\N	0	0		0	0	\N	0	\N	\N
4	Footer navigation	container	default	0	0	5		N;			\N		2006-09-30 09:56:36	2011-01-16 17:25:26	1	1	1000		0	N;			N;	N;	1	\N	0	0		0	0	\N	0	\N	\N
83	Novinky	page	default	88	0	30		\N					2006-09-30 12:07:59	2011-01-16 17:32:03	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	0	0		0	0	\N	0	\N	\N
20	Kontakt	page	default	88	0	20		\N					2006-09-30 12:07:59	2011-01-16 17:26:22	1	1	1000		0	N;		fibonacci-1-1	N;	N;	1	0	\N	0		0	0	\N	0	\N	\N
23	O nás	page	default	88	0	35		\N					2006-09-30 12:09:30	2011-01-16 17:26:56	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	\N	0		0	0	\N	0	\N	\N
9	Obnovení hesla	page	default	2	0	0		\N					2006-09-30 10:35:15	2008-08-24 22:36:37	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	\N	0		0	1	\N	0	\N	\N
13	Registrace	page	default	2	0	0		\N					2006-09-30 10:36:09	2008-08-24 22:37:49	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	\N	0		0	1	\N	0	\N	\N
8	Přihlášení	page	default	2	0	0		\N					2006-09-30 10:35:02	2008-08-24 23:11:13	1	1	1000		0	N;	pageLogin	fibonacci-2-1	N;	N;	1	0	\N	0		0	1	\N	0	\N	\N
6	Nákupní košík	page	default	2	0	0		\N					2006-09-30 10:34:35	2008-08-24 22:35:09	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	\N	0		0	0	\N	0	\N	\N
19	Detail	page	default	17	0	0		\N					2006-09-30 12:04:12	2008-08-24 22:36:12	1	0	1000		0	N;		fibonacci-2-1	N;	N;	1	0	1	0		0	1	\N	0	\N	\N
17	Moje objednávky	page	default	15	0	0		\N					2006-09-30 12:03:28	2008-08-24 23:11:45	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	1	0		0	1	\N	0	\N	\N
88	Global navigation	container	default	0	0	15		\N					2009-08-16 13:05:12	2011-01-16 17:25:15	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	0	0		0	0	\N	0	\N	\N
1013	Laboris nisi ut aliquip	page	news	83	0	0	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\n<ul>\n<li>velit esse cillum dolore</li>\n<li>consectetur adipisicing elit</li>\n<li>occaecat cupidatat non proident</li>\n</ul>\n<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>					2008-08-16 03:59:19	2011-01-16 17:33:41	1	1	1000		0	N;		fibonacci-2-1	a:2:{s:6:"author";s:0:"";s:13:"allow_comment";i:1;}	N;	1	\N	0	0		0	0	\N	0	\N	\N
15	Můj účet	page	default	88	0	10		\N					2006-09-30 12:02:53	2009-08-16 13:05:58	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	1	0		0	1	\N	0	\N	\N
1026	Stránka 1	page	default	1	0	0	\N	\N	\N	\N	\N	\N	2011-01-16 17:27:11	2011-01-16 17:27:11	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	1	\N	\N	0		0	0	\N	0	\N	\N
1027	Stránka 2	page	default	1	0	0	\N	\N	\N	\N	\N	\N	2011-01-16 17:27:18	2011-01-16 17:27:18	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	1	\N	\N	0		0	0	\N	0	\N	\N
1028	Stránka 3	page	default	1	0	0	\N	\N	\N	\N	\N	\N	2011-01-16 17:27:25	2011-01-16 17:27:25	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	1	\N	\N	0		0	0	\N	0	\N	\N
1025	Ochrana údajů	page	default	4	0	0		\N			Ochrana osobních údajů		2011-01-16 17:25:46	2011-01-16 17:28:08	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	0	0		0	0	\N	0	\N	\N
1014	Excepteur sint occaecat	page	news	83	0	0	<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\n<ul>\n<li>velit esse cillum dolore</li>\n<li>consectetur adipisicing elit</li>\n<li>occaecat cupidatat non proident</li>\n</ul>\n<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>					2008-08-16 03:59:48	2011-01-16 17:33:30	1	1	1000		0	N;		fibonacci-2-1	a:2:{s:6:"author";s:0:"";s:13:"allow_comment";i:1;}	N;	1	\N	0	0		0	0	\N	0	\N	\N
5	Úvod	page	default	88	0	40		\N			Prázdný web		2006-09-30 10:02:51	2011-12-13 14:57:05	1	1	1000		0	N;		fibonacci-2-1	N;	N;	0	0	0	0		0	0		0	\N	\N
54	User pref component	content	component	18	1	0	\N				\N		2006-09-30 15:25:21	2008-08-24 18:25:48	1	1	1000		0	N;			a:3:{s:8:"template";s:16:"client/edit.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
94	Bin	page	default	0	0	0		\N					2014-12-07 00:00:00	2014-12-07 00:00:00	0	0	1000		0	\N			\N	\N	1	0	0	0		0	0		0	\N	\N
\.


--
-- Name: common_node_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_node_id_seq', 1030, true);


--
-- Data for Name: common_node_taxonomy; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_node_taxonomy (id, node_id, taxonomy_tree_id) FROM stdin;
\.


--
-- Name: common_node_taxonomy_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_node_taxonomy_id_seq', 1, false);


--
-- Data for Name: common_print_article; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_print_article (id, src, role, node_id, title, description, priority, modified, author, type, authors, issue_number, page_from, date, other) FROM stdin;
\.


--
-- Name: common_print_article_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_print_article_id_seq', 1, false);


--
-- Data for Name: common_revision; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_revision (id, object, node_id, content, status, customer_id, created, modified, other_data) FROM stdin;
\.


--
-- Name: common_revision_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_revision_id_seq', 1, false);


--
-- Data for Name: common_scheduler; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_scheduler (id, node_id, node_type, controller, parameters, scheduled_time, status, lock_token, result, start_time, completed_time, created, modified) FROM stdin;
\.


--
-- Name: common_scheduler_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_scheduler_id_seq', 1, false);


--
-- Data for Name: common_session; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_session (id, session_id, session_data, customer_id, created, modified, ip_address, php_auth_user, http_referer, http_user_agent) FROM stdin;
\.


--
-- Data for Name: common_session_archive; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_session_archive (id, session_id, session_data, customer_id, created, modified, ip_address, php_auth_user, http_referer, http_user_agent) FROM stdin;
\.


--
-- Name: common_session_archive_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_session_archive_id_seq', 1, false);


--
-- Name: common_session_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_session_id_seq', 19, true);


--
-- Data for Name: common_taxonomy_label; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_taxonomy_label (id, title, description, priority, publish) FROM stdin;
0	Root		0	1
1	Brands		0	1
2	Products categories		0	1
3	Blog categories		0	1
\.


--
-- Name: common_taxonomy_label_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_taxonomy_label_id_seq', 3, true);


--
-- Data for Name: common_taxonomy_label_image; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_taxonomy_label_image (id, src, role, node_id, title, description, priority, modified, author, content, other_data, link_to_node_id, customer_id) FROM stdin;
\.


--
-- Name: common_taxonomy_label_image_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_taxonomy_label_image_id_seq', 1, false);


--
-- Data for Name: common_taxonomy_tree; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_taxonomy_tree (id, label_id, parent, priority, publish) FROM stdin;
1	1	\N	0	1
2	2	\N	0	1
3	3	\N	0	1
\.


--
-- Name: common_taxonomy_tree_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_taxonomy_tree_id_seq', 3, true);


--
-- Data for Name: common_uri_mapping; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_uri_mapping (id, node_id, public_uri, type) FROM stdin;
95	1013	/novinky/2008/08/16/laboris-nisi-ut-aliquip	generic
96	1014	/novinky/2008/08/16/excepteur-sint-occaecat	generic
98	1025	/ochrana-udaju	generic
82	15	/global-navigation/muj-ucet	generic
72	5	/uvod	generic
73	6	/nakupni-kosik	generic
74	7	/provedeni-objednavky	generic
75	8	/prihlaseni	generic
76	9	/obnoveni-hesla	generic
77	10	/platba	generic
78	11	/selhani-platby	generic
79	12	/platba-probehla	generic
80	13	/registrace	generic
81	14	/404	generic
83	16	/global-navigation/muj-ucet/sprava-adres	generic
84	17	/global-navigation/muj-ucet/moje-objednavky	generic
85	18	/global-navigation/muj-ucet/osobni-udaje	generic
86	19	/global-navigation/muj-ucet/moje-objednavky/detail	generic
87	20	/kontakt	generic
88	21	/vyhledat	generic
89	22	/mapa-stranek	generic
90	23	/o-nas	generic
91	26	/obchodni-podminky	generic
92	83	/novinky	generic
93	84	/articles	generic
94	85	/content-bits	generic
97	88	/global-navigation	generic
99	1026	/stranka-1	generic
100	1027	/stranka-2	generic
101	1028	/stranka-3	generic
102	94	/bin	generic
\.


--
-- Name: common_uri_mapping_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_uri_mapping_id_seq', 102, true);


--
-- Data for Name: common_watchdog; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_watchdog (id, name, watched_item_id, customer_id, created, modified, publish, other_data) FROM stdin;
\.


--
-- Name: common_watchdog_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_watchdog_id_seq', 1, false);


--
-- Data for Name: ecommerce_basket; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_basket (id, customer_id, created, note, ip_address, face_value_voucher, title, other_data) FROM stdin;
\.


--
-- Data for Name: ecommerce_basket_content; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_basket_content (id, basket_id, product_variety_id, quantity, price_id, other_data, product_type_id, discount) FROM stdin;
\.


--
-- Name: ecommerce_basket_content_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_basket_content_id_seq', 1, false);


--
-- Name: ecommerce_basket_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_basket_id_seq', 1, false);


--
-- Data for Name: ecommerce_delivery; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_delivery (id, order_id, carrier_id, value_net, vat, vat_rate, required_datetime, note_customer, note_backoffice, other_data, weight) FROM stdin;
\.


--
-- Data for Name: ecommerce_delivery_carrier; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_delivery_carrier (id, title, description, priority, publish, zone_id, order_value_from, order_value_to) FROM stdin;
1	Standard	\N	0	0	1	\N	\N
2	Royal Mail 1st Class Post	\N	0	1	1	\N	\N
3	DHL Courier	\N	0	1	1	\N	\N
4	UPS	\N	0	0	1	\N	\N
5	Courier	\N	0	0	1	\N	\N
6	Download	\N	0	0	1	\N	\N
\.


--
-- Name: ecommerce_delivery_carrier_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_delivery_carrier_id_seq', 6, true);


--
-- Data for Name: ecommerce_delivery_carrier_rate; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_delivery_carrier_rate (id, carrier_id, weight_from, weight_to, price) FROM stdin;
\.


--
-- Name: ecommerce_delivery_carrier_rate_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_delivery_carrier_rate_id_seq', 1, false);


--
-- Data for Name: ecommerce_delivery_carrier_zone; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_delivery_carrier_zone (id, name) FROM stdin;
1	Legacy
2	UK
3	Europe
4	World
\.


--
-- Name: ecommerce_delivery_carrier_zone_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_delivery_carrier_zone_id_seq', 1, true);


--
-- Data for Name: ecommerce_delivery_carrier_zone_to_country; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_delivery_carrier_zone_to_country (id, country_id, zone_id) FROM stdin;
1	222	2
2	2	3
3	5	3
4	11	3
5	14	3
6	15	3
7	20	3
8	21	3
9	33	3
10	53	3
11	55	3
12	56	3
13	57	3
14	67	3
15	72	3
16	73	3
17	80	3
18	81	3
19	83	3
20	84	3
21	85	3
22	97	3
23	98	3
24	103	3
25	105	3
26	109	3
27	115	3
28	117	3
29	122	3
30	123	3
31	124	3
32	126	3
33	74	3
34	132	3
35	140	3
36	141	3
37	240	3
38	150	3
39	160	3
40	170	3
41	171	3
42	175	3
43	176	3
44	182	3
45	241	3
46	189	3
47	190	3
48	195	3
49	203	3
50	204	3
51	207	3
52	215	3
53	216	3
54	220	3
55	226	3
56	228	3
57	1	4
58	3	4
59	4	4
60	6	4
61	7	4
62	8	4
63	9	4
64	10	4
65	12	4
66	13	4
67	16	4
68	17	4
69	18	4
70	19	4
71	22	4
72	23	4
73	24	4
74	25	4
75	26	4
76	27	4
77	28	4
78	29	4
79	30	4
80	31	4
81	32	4
82	34	4
83	35	4
84	36	4
85	37	4
86	38	4
87	39	4
88	40	4
89	41	4
90	42	4
91	43	4
92	44	4
93	45	4
94	46	4
95	47	4
96	48	4
97	49	4
98	50	4
99	51	4
100	52	4
101	54	4
102	58	4
103	59	4
104	60	4
105	61	4
106	62	4
107	63	4
108	64	4
109	65	4
110	66	4
111	68	4
112	69	4
113	70	4
114	71	4
115	75	4
116	76	4
117	77	4
118	78	4
119	79	4
120	82	4
121	86	4
122	87	4
123	88	4
124	89	4
125	90	4
126	91	4
127	92	4
128	93	4
129	94	4
130	95	4
131	96	4
132	99	4
133	100	4
134	101	4
135	102	4
136	104	4
137	106	4
138	107	4
139	108	4
140	110	4
141	111	4
142	112	4
143	113	4
144	114	4
145	116	4
146	118	4
147	119	4
148	120	4
149	121	4
150	125	4
151	127	4
152	128	4
153	129	4
154	130	4
155	131	4
156	133	4
157	134	4
158	135	4
159	136	4
160	137	4
161	138	4
162	139	4
163	142	4
164	143	4
165	144	4
166	145	4
167	146	4
168	147	4
169	148	4
170	149	4
171	151	4
172	152	4
173	153	4
174	154	4
175	155	4
176	156	4
177	157	4
178	158	4
179	159	4
180	161	4
181	162	4
182	163	4
183	164	4
184	165	4
185	166	4
186	167	4
187	168	4
188	169	4
189	172	4
190	173	4
191	174	4
192	177	4
193	178	4
194	179	4
195	180	4
196	181	4
197	183	4
198	184	4
199	185	4
200	186	4
201	187	4
202	188	4
203	191	4
204	192	4
205	193	4
206	194	4
207	196	4
208	197	4
209	198	4
210	199	4
211	200	4
212	201	4
213	202	4
214	205	4
215	206	4
216	208	4
217	209	4
218	210	4
219	211	4
220	212	4
221	213	4
222	214	4
223	217	4
224	218	4
225	219	4
226	221	4
227	223	4
228	224	4
229	225	4
230	227	4
231	229	4
232	230	4
233	231	4
234	232	4
235	233	4
236	234	4
237	235	4
238	236	4
239	237	4
240	238	4
241	239	4
\.


--
-- Name: ecommerce_delivery_carrier_zone_to_country_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_delivery_carrier_zone_to_country_id_seq', 241, true);


--
-- Name: ecommerce_delivery_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_delivery_id_seq', 1, false);


--
-- Data for Name: ecommerce_invoice; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_invoice (id, order_id, goods_net, goods_vat, delivery_net, delivery_vat, payment_amount, payment_type, created, modified, status, other_data, basket_detail, customer_name, customer_email, address_invoice, address_delivery, face_value_voucher, basket_detail_enhanced) FROM stdin;
\.


--
-- Name: ecommerce_invoice_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_invoice_id_seq', 1, false);


--
-- Data for Name: ecommerce_offer; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_offer (id, description, product_variety_id, schedule_start, schedule_end, campaign_category_id, roundel_category_id, price_id, quantity, saving, created, modified, other_data, offer_group_id, priority) FROM stdin;
\.


--
-- Data for Name: ecommerce_offer_group; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_offer_group (id, title, description, schedule_start, schedule_end, publish, created, modified, other_data) FROM stdin;
\.


--
-- Name: ecommerce_offer_group_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_offer_group_id_seq', 1, false);


--
-- Name: ecommerce_offer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_offer_id_seq', 1, false);


--
-- Data for Name: ecommerce_order; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_order (id, basket_id, invoices_address_id, delivery_address_id, other_data, status, note_customer, note_backoffice, php_session_id, referrer, payment_type, created, modified, review_email_sent) FROM stdin;
\.


--
-- Name: ecommerce_order_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_order_id_seq', 1, false);


--
-- Data for Name: ecommerce_order_log; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_order_log (id, order_id, status, datetime, description, other_data) FROM stdin;
\.


--
-- Name: ecommerce_order_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_order_log_id_seq', 1, false);


--
-- Data for Name: ecommerce_price; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_price (id, product_variety_id, currency_code, value, type, date) FROM stdin;
\.


--
-- Name: ecommerce_price_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_price_id_seq', 1, false);


--
-- Data for Name: ecommerce_product; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_product (id, name, teaser, description, url, priority, publish, other_data, modified, availability, name_aka) FROM stdin;
\.


--
-- Name: ecommerce_product_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_product_id_seq', 1, false);


--
-- Data for Name: ecommerce_product_image; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_product_image (id, src, role, node_id, title, description, priority, modified, author, content, other_data, link_to_node_id, customer_id) FROM stdin;
\.


--
-- Name: ecommerce_product_image_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_product_image_id_seq', 1, false);


--
-- Data for Name: ecommerce_product_review; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_product_review (id, parent, node_id, title, content, author_name, author_email, author_website, author_ip_address, customer_id, created, publish, rating, relation_subject) FROM stdin;
\.


--
-- Name: ecommerce_product_review_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_product_review_id_seq', 1, false);


--
-- Data for Name: ecommerce_product_taxonomy; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_product_taxonomy (id, node_id, taxonomy_tree_id) FROM stdin;
\.


--
-- Name: ecommerce_product_taxonomy_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_product_taxonomy_id_seq', 1, false);


--
-- Data for Name: ecommerce_product_to_product; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_product_to_product (id, product_id, related_product_id) FROM stdin;
\.


--
-- Name: ecommerce_product_to_product_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_product_to_product_id_seq', 1, false);


--
-- Data for Name: ecommerce_product_type; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_product_type (id, name, vat, publish) FROM stdin;
11	Generic 0	0	1
1	Hardware	17.5	0
2	Software	17.5	0
3	Energy	5	0
4	Software (only download)	17.5	0
5	Documents  (download)	17.5	0
6	books	0	0
7	Food	17.5	0
8	Food BIO	5	0
9	Generic 1	22	1
10	Generic 2	9	1
\.


--
-- Name: ecommerce_product_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_product_type_id_seq', 11, true);


--
-- Data for Name: ecommerce_product_variety; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_product_variety (id, name, product_id, sku, weight, weight_gross, stock, priority, description, other_data, width, height, depth, diameter, modified, publish, display_permission, ean13, upc, condition, wholesale, reward_points, subtitle, product_type_id, limit_to_delivery_zones) FROM stdin;
\.


--
-- Name: ecommerce_product_variety_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_product_variety_id_seq', 1, false);


--
-- Data for Name: ecommerce_product_variety_image; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_product_variety_image (id, src, role, node_id, title, description, priority, modified, author, content, other_data, link_to_node_id, customer_id) FROM stdin;
\.


--
-- Name: ecommerce_product_variety_image_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_product_variety_image_id_seq', 1, false);


--
-- Data for Name: ecommerce_product_variety_taxonomy; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_product_variety_taxonomy (id, node_id, taxonomy_tree_id) FROM stdin;
\.


--
-- Name: ecommerce_product_variety_taxonomy_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_product_variety_taxonomy_id_seq', 1, false);


--
-- Data for Name: ecommerce_promotion; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_promotion (id, title, description, publish, created, modified, customer_account_type, code_pattern, discount_fixed_value, discount_percentage_value, discount_free_delivery, uses_per_coupon, uses_per_customer, limit_list_products, other_data, limit_delivery_country_id, limit_delivery_carrier_id, generated_by_order_id, generated_by_customer_id, limit_by_customer_id, limit_to_first_order, limit_to_order_amount, type, limit_cumulative_discount, free_promo_products) FROM stdin;
\.


--
-- Data for Name: ecommerce_promotion_code; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_promotion_code (id, promotion_id, code, order_id) FROM stdin;
\.


--
-- Name: ecommerce_promotion_code_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_promotion_code_id_seq', 1, false);


--
-- Name: ecommerce_promotion_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_promotion_id_seq', 1, false);


--
-- Data for Name: ecommerce_promotion_type; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_promotion_type (id, title, description, taxable, publish, created, modified, other_data) FROM stdin;
1	Discount Coupon		0	1	2013-11-22 17:18:14	2013-11-22 17:18:14	
2	Referral Invite Coupon		0	1	2013-11-22 17:18:14	2013-11-22 17:18:14	
3	Referral Reward Coupon		0	1	2013-11-22 17:18:14	2013-11-22 17:18:14	
4	Gift Voucher		1	1	2013-11-22 17:18:14	2013-11-22 17:18:14	
\.


--
-- Name: ecommerce_promotion_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_promotion_type_id_seq', 1, false);


--
-- Data for Name: ecommerce_recipe; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_recipe (id, title, description, instructions, video_url, serving_people, preparation_time, cooking_time, priority, created, modified, publish, other_data) FROM stdin;
\.


--
-- Name: ecommerce_recipe_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_recipe_id_seq', 1, false);


--
-- Data for Name: ecommerce_recipe_image; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_recipe_image (id, src, role, node_id, title, description, priority, modified, author, content, other_data, link_to_node_id, customer_id) FROM stdin;
\.


--
-- Name: ecommerce_recipe_image_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_recipe_image_id_seq', 1, false);


--
-- Data for Name: ecommerce_recipe_ingredients; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_recipe_ingredients (id, recipe_id, product_variety_id, quantity, units, notes, group_title) FROM stdin;
\.


--
-- Name: ecommerce_recipe_ingredients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_recipe_ingredients_id_seq', 1, false);


--
-- Data for Name: ecommerce_recipe_review; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_recipe_review (id, parent, node_id, title, content, author_name, author_email, author_website, author_ip_address, customer_id, created, publish, rating, relation_subject) FROM stdin;
\.


--
-- Name: ecommerce_recipe_review_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_recipe_review_id_seq', 1, false);


--
-- Data for Name: ecommerce_recipe_taxonomy; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_recipe_taxonomy (id, node_id, taxonomy_tree_id) FROM stdin;
\.


--
-- Name: ecommerce_recipe_taxonomy_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_recipe_taxonomy_id_seq', 1, false);


--
-- Data for Name: ecommerce_store; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_store (id, title, description, address, opening_hours, telephone, manager_name, email, type_id, coordinates_x, coordinates_y, latitude, longitude, created, modified, publish, street_view_options, other_data, country_id, address_name, address_line_1, address_line_2, address_line_3, address_city, address_county, address_post_code, code, url) FROM stdin;
\.


--
-- Name: ecommerce_store_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_store_id_seq', 1, false);


--
-- Data for Name: ecommerce_store_image; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_store_image (id, src, role, node_id, title, description, priority, modified, author, content, other_data, link_to_node_id, customer_id) FROM stdin;
\.


--
-- Name: ecommerce_store_image_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_store_image_id_seq', 1, false);


--
-- Data for Name: ecommerce_store_taxonomy; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_store_taxonomy (id, node_id, taxonomy_tree_id) FROM stdin;
\.


--
-- Name: ecommerce_store_taxonomy_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_store_taxonomy_id_seq', 1, false);


--
-- Data for Name: ecommerce_store_type; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_store_type (id, title, description, publish, created, modified, other_data) FROM stdin;
1	Store		1	2013-11-22 17:18:14	2013-11-22 17:18:14	
2	Supplier		1	2013-11-22 17:18:14	2013-11-22 17:18:14	
\.


--
-- Name: ecommerce_store_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_store_type_id_seq', 1, false);


--
-- Data for Name: ecommerce_transaction; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_transaction (id, order_id, pg_data, currency_code, amount, created, type, status) FROM stdin;
\.


--
-- Name: ecommerce_transaction_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_transaction_id_seq', 1, false);


--
-- Data for Name: education_survey; Type: TABLE DATA; Schema: public; Owner: -
--

COPY education_survey (id, title, description, created, modified, priority, publish, other_data) FROM stdin;
\.


--
-- Data for Name: education_survey_entry; Type: TABLE DATA; Schema: public; Owner: -
--

COPY education_survey_entry (id, survey_id, customer_id, relation_subject, created, modified, publish, ip_address, session_id, other_data) FROM stdin;
\.


--
-- Data for Name: education_survey_entry_answer; Type: TABLE DATA; Schema: public; Owner: -
--

COPY education_survey_entry_answer (id, survey_entry_id, question_id, question_answer_id, value, created, modified, publish) FROM stdin;
\.


--
-- Name: education_survey_entry_answer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('education_survey_entry_answer_id_seq', 1, false);


--
-- Name: education_survey_entry_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('education_survey_entry_id_seq', 1, false);


--
-- Name: education_survey_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('education_survey_id_seq', 1, false);


--
-- Data for Name: education_survey_image; Type: TABLE DATA; Schema: public; Owner: -
--

COPY education_survey_image (id, src, role, node_id, title, description, priority, modified, author, content, other_data, link_to_node_id, customer_id) FROM stdin;
\.


--
-- Name: education_survey_image_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('education_survey_image_id_seq', 1, false);


--
-- Data for Name: education_survey_question; Type: TABLE DATA; Schema: public; Owner: -
--

COPY education_survey_question (id, survey_id, parent, step, title, description, mandatory, type, priority, publish, weight, content, other_data) FROM stdin;
\.


--
-- Data for Name: education_survey_question_answer; Type: TABLE DATA; Schema: public; Owner: -
--

COPY education_survey_question_answer (id, question_id, title, description, is_correct, points, priority, publish, content, other_data) FROM stdin;
\.


--
-- Name: education_survey_question_answer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('education_survey_question_answer_id_seq', 1, false);


--
-- Name: education_survey_question_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('education_survey_question_id_seq', 1, false);


--
-- Data for Name: international_country; Type: TABLE DATA; Schema: public; Owner: -
--

COPY international_country (id, name, iso_code2, iso_code3, eu_status, currency_code, publish) FROM stdin;
1	Afghanistan	AF	AFG	f	\N	1
2	Albania	AL	ALB	f	\N	1
3	Algeria	DZ	DZA	f	\N	1
4	American Samoa	AS	ASM	f	\N	1
5	Andorra	AD	AND	f	\N	1
6	Angola	AO	AGO	f	\N	1
7	Anguilla	AI	AIA	f	\N	1
8	Antarctica	AQ	ATA	f	\N	1
9	Antigua and Barbuda	AG	ATG	f	\N	1
10	Argentina	AR	ARG	f	\N	1
11	Armenia	AM	ARM	f	\N	1
12	Aruba	AW	ABW	f	\N	1
13	Australia	AU	AUS	f	\N	1
14	Austria	AT	AUT	t	\N	1
15	Azerbaijan	AZ	AZE	f	\N	1
16	Bahamas	BS	BHS	f	\N	1
17	Bahrain	BH	BHR	f	\N	1
18	Bangladesh	BD	BGD	f	\N	1
19	Barbados	BB	BRB	f	\N	1
20	Belarus	BY	BLR	f	\N	1
21	Belgium	BE	BEL	t	\N	1
22	Belize	BZ	BLZ	f	\N	1
23	Benin	BJ	BEN	f	\N	1
24	Bermuda	BM	BMU	f	\N	1
25	Bhutan	BT	BTN	f	\N	1
26	Bolivia	BO	BOL	f	\N	1
27	Bosnia and Herzegowina	BA	BIH	f	\N	1
28	Botswana	BW	BWA	f	\N	1
29	Bouvet Island	BV	BVT	f	\N	1
30	Brazil	BR	BRA	f	\N	1
31	British Indian Ocean Territory	IO	IOT	f	\N	1
32	Brunei Darussalam	BN	BRN	f	\N	1
34	Burkina Faso	BF	BFA	f	\N	1
35	Burundi	BI	BDI	f	\N	1
36	Cambodia	KH	KHM	f	\N	1
37	Cameroon	CM	CMR	f	\N	1
38	Canada	CA	CAN	f	\N	1
39	Cape Verde	CV	CPV	f	\N	1
40	Cayman Islands	KY	CYM	f	\N	1
41	Central African Republic	CF	CAF	f	\N	1
42	Chad	TD	TCD	f	\N	1
43	Chile	CL	CHL	f	\N	1
44	China	CN	CHN	f	\N	1
45	Christmas Island	CX	CXR	f	\N	1
46	Cocos (Keeling) Islands	CC	CCK	f	\N	1
47	Colombia	CO	COL	f	\N	1
48	Comoros	KM	COM	f	\N	1
49	Congo	CG	COG	f	\N	1
50	Cook Islands	CK	COK	f	\N	1
51	Costa Rica	CR	CRI	f	\N	1
52	Cote D'Ivoire	CI	CIV	f	\N	1
53	Croatia	HR	HRV	f	\N	1
54	Cuba	CU	CUB	f	\N	1
55	Cyprus	CY	CYP	t	\N	1
56	Czech Republic	CZ	CZE	t	\N	1
57	Denmark	DK	DNK	t	\N	1
58	Djibouti	DJ	DJI	f	\N	1
59	Dominica	DM	DMA	f	\N	1
60	Dominican Republic	DO	DOM	f	\N	1
61	East Timor	TP	TMP	f	\N	1
62	Ecuador	EC	ECU	f	\N	1
63	Egypt	EG	EGY	f	\N	1
64	El Salvador	SV	SLV	f	\N	1
65	Equatorial Guinea	GQ	GNQ	f	\N	1
66	Eritrea	ER	ERI	f	\N	1
67	Estonia	EE	EST	t	\N	1
68	Ethiopia	ET	ETH	f	\N	1
69	Falkland Islands (Malvinas)	FK	FLK	f	\N	1
70	Faroe Islands	FO	FRO	f	\N	1
71	Fiji	FJ	FJI	f	\N	1
72	Finland	FI	FIN	t	\N	1
73	France	FR	FRA	t	\N	1
75	French Guiana	GF	GUF	f	\N	1
76	French Polynesia	PF	PYF	f	\N	1
77	French Southern Territories	TF	ATF	f	\N	1
78	Gabon	GA	GAB	f	\N	1
79	Gambia	GM	GMB	f	\N	1
80	Georgia	GE	GEO	f	\N	1
81	Germany	DE	DEU	t	\N	1
82	Ghana	GH	GHA	f	\N	1
83	Gibraltar	GI	GIB	f	\N	1
84	Greece	GR	GRC	t	\N	1
85	Greenland	GL	GRL	f	\N	1
86	Grenada	GD	GRD	f	\N	1
87	Guadeloupe	GP	GLP	f	\N	1
88	Guam	GU	GUM	f	\N	1
89	Guatemala	GT	GTM	f	\N	1
90	Guinea	GN	GIN	f	\N	1
91	Guinea-bissau	GW	GNB	f	\N	1
92	Guyana	GY	GUY	f	\N	1
93	Haiti	HT	HTI	f	\N	1
94	Heard and Mc Donald Islands	HM	HMD	f	\N	1
95	Honduras	HN	HND	f	\N	1
96	Hong Kong	HK	HKG	f	\N	1
97	Hungary	HU	HUN	t	\N	1
98	Iceland	IS	ISL	f	\N	1
99	India	IN	IND	f	\N	1
100	Indonesia	ID	IDN	f	\N	1
101	Iran (Islamic Republic of)	IR	IRN	f	\N	1
102	Iraq	IQ	IRQ	f	\N	1
103	Ireland	IE	IRL	t	\N	1
104	Israel	IL	ISR	f	\N	1
105	Italy	IT	ITA	t	\N	1
106	Jamaica	JM	JAM	f	\N	1
107	Japan	JP	JPN	f	\N	1
108	Jordan	JO	JOR	f	\N	1
109	Kazakhstan	KZ	KAZ	f	\N	1
110	Kenya	KE	KEN	f	\N	1
111	Kiribati	KI	KIR	f	\N	1
112	Korea, Democratic People's Republic of	KP	PRK	f	\N	1
113	Korea, Republic of	KR	KOR	f	\N	1
114	Kuwait	KW	KWT	f	\N	1
115	Kyrgyzstan	KG	KGZ	f	\N	1
116	Lao People's Democratic Republic	LA	LAO	f	\N	1
117	Latvia	LV	LVA	t	\N	1
118	Lebanon	LB	LBN	f	\N	1
119	Lesotho	LS	LSO	f	\N	1
120	Liberia	LR	LBR	f	\N	1
121	Libyan Arab Jamahiriya	LY	LBY	f	\N	1
122	Liechtenstein	LI	LIE	f	\N	1
123	Lithuania	LT	LTU	t	\N	1
124	Luxembourg	LU	LUX	t	\N	1
125	Macau	MO	MAC	f	\N	1
126	Macedonia	MK	MKD	f	\N	1
127	Madagascar	MG	MDG	f	\N	1
128	Malawi	MW	MWI	f	\N	1
129	Malaysia	MY	MYS	f	\N	1
130	Maldives	MV	MDV	f	\N	1
131	Mali	ML	MLI	f	\N	1
132	Malta	MT	MLT	t	\N	1
133	Marshall Islands	MH	MHL	f	\N	1
134	Martinique	MQ	MTQ	f	\N	1
135	Mauritania	MR	MRT	f	\N	1
136	Mauritius	MU	MUS	f	\N	1
137	Mayotte	YT	MYT	f	\N	1
138	Mexico	MX	MEX	f	\N	1
139	Micronesia	FM	FSM	f	\N	1
140	Moldova	MD	MDA	f	\N	1
141	Monaco	MC	MCO	f	\N	1
142	Mongolia	MN	MNG	f	\N	1
143	Montserrat	MS	MSR	f	\N	1
144	Morocco	MA	MAR	f	\N	1
145	Mozambique	MZ	MOZ	f	\N	1
146	Myanmar	MM	MMR	f	\N	1
147	Namibia	NA	NAM	f	\N	1
148	Nauru	NR	NRU	f	\N	1
149	Nepal	NP	NPL	f	\N	1
150	Netherlands	NL	NLD	t	\N	1
151	Netherlands Antilles	AN	ANT	f	\N	1
152	New Caledonia	NC	NCL	f	\N	1
153	New Zealand	NZ	NZL	f	\N	1
154	Nicaragua	NI	NIC	f	\N	1
155	Niger	NE	NER	f	\N	1
156	Nigeria	NG	NGA	f	\N	1
157	Niue	NU	NIU	f	\N	1
158	Norfolk Island	NF	NFK	f	\N	1
159	Northern Mariana Islands	MP	MNP	f	\N	1
160	Norway	NO	NOR	f	\N	1
161	Oman	OM	OMN	f	\N	1
162	Pakistan	PK	PAK	f	\N	1
163	Palau	PW	PLW	f	\N	1
164	Panama	PA	PAN	f	\N	1
165	Papua New Guinea	PG	PNG	f	\N	1
166	Paraguay	PY	PRY	f	\N	1
167	Peru	PE	PER	f	\N	1
168	Philippines	PH	PHL	f	\N	1
169	Pitcairn	PN	PCN	f	\N	1
170	Poland	PL	POL	t	\N	1
171	Portugal	PT	PRT	t	\N	1
172	Puerto Rico	PR	PRI	f	\N	1
173	Qatar	QA	QAT	f	\N	1
174	Reunion	RE	REU	f	\N	1
176	Russia	RU	RUS	f	\N	1
177	Rwanda	RW	RWA	f	\N	1
178	Saint Kitts and Nevis	KN	KNA	f	\N	1
179	Saint Lucia	LC	LCA	f	\N	1
180	Saint Vincent and the Grenadines	VC	VCT	f	\N	1
181	Samoa	WS	WSM	f	\N	1
182	San Marino	SM	SMR	f	\N	1
183	Sao Tome and Principe	ST	STP	f	\N	1
184	Saudi Arabia	SA	SAU	f	\N	1
185	Senegal	SN	SEN	f	\N	1
186	Seychelles	SC	SYC	f	\N	1
187	Sierra Leone	SL	SLE	f	\N	1
188	Singapore	SG	SGP	f	\N	1
189	Slovakia (Slovak Republic)	SK	SVK	t	\N	1
190	Slovenia	SI	SVN	t	\N	1
191	Solomon Islands	SB	SLB	f	\N	1
192	Somalia	SO	SOM	f	\N	1
193	South Africa	ZA	ZAF	f	\N	1
194	South Georgia and the South Sandwich Islands	GS	SGS	f	\N	1
195	Spain	ES	ESP	t	\N	1
196	Sri Lanka	LK	LKA	f	\N	1
197	St. Helena	SH	SHN	f	\N	1
198	St. Pierre and Miquelon	PM	SPM	f	\N	1
199	Sudan	SD	SDN	f	\N	1
200	Suriname	SR	SUR	f	\N	1
201	Svalbard and Jan Mayen Islands	SJ	SJM	f	\N	1
202	Swaziland	SZ	SWZ	f	\N	1
203	Sweden	SE	SWE	t	\N	1
204	Switzerland	CH	CHE	f	\N	1
205	Syrian Arab Republic	SY	SYR	f	\N	1
206	Taiwan	TW	TWN	f	\N	1
207	Tajikistan	TJ	TJK	f	\N	1
208	Tanzania, United Republic of	TZ	TZA	f	\N	1
209	Thailand	TH	THA	f	\N	1
210	Togo	TG	TGO	f	\N	1
211	Tokelau	TK	TKL	f	\N	1
212	Tonga	TO	TON	f	\N	1
213	Trinidad and Tobago	TT	TTO	f	\N	1
214	Tunisia	TN	TUN	f	\N	1
215	Turkey	TR	TUR	f	\N	1
216	Turkmenistan	TM	TKM	f	\N	1
217	Turks and Caicos Islands	TC	TCA	f	\N	1
218	Tuvalu	TV	TUV	f	\N	1
219	Uganda	UG	UGA	f	\N	1
220	Ukraine	UA	UKR	f	\N	1
221	United Arab Emirates	AE	ARE	f	\N	1
222	United Kingdom	GB	GBR	t	\N	1
223	United States	US	USA	f	\N	1
224	United States Minor Outlying Islands	UM	UMI	f	\N	1
225	Uruguay	UY	URY	f	\N	1
226	Uzbekistan	UZ	UZB	f	\N	1
227	Vanuatu	VU	VUT	f	\N	1
228	Vatican City State (Holy See)	VA	VAT	f	\N	1
229	Venezuela	VE	VEN	f	\N	1
230	Viet Nam	VN	VNM	f	\N	1
231	Virgin Islands (British)	VG	VGB	f	\N	1
232	Virgin Islands (U.S.)	VI	VIR	f	\N	1
233	Wallis and Futuna Islands	WF	WLF	f	\N	1
234	Western Sahara	EH	ESH	f	\N	1
235	Yemen	YE	YEM	f	\N	1
236	Yugoslavia	YU	YUG	f	\N	1
237	Zaire	ZR	ZAR	f	\N	1
238	Zambia	ZM	ZMB	f	\N	1
239	Zimbabwe	ZW	ZWE	f	\N	1
74	Madeira	XM	MDR	f	\N	1
240	Montenegro	ME	MNE	f	\N	1
241	Serbia	RS	SRB	f	\N	1
33	Bulgaria	BG	BGR	t	\N	1
175	Romania	RO	ROM	t	\N	1
\.


--
-- Name: international_country_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('international_country_id_seq', 241, true);


--
-- Data for Name: international_currency; Type: TABLE DATA; Schema: public; Owner: -
--

COPY international_currency (id, code, name, symbol_left, symbol_right) FROM stdin;
1	EUR	Euro	&euro;	\N
2	GBP	British Pound	&pound;	\N
3	AFA	Afghanistan Afghani		\N
4	ALL	Albanian Lek		\N
5	DZD	Algerian Dinar		\N
6	ADF	Andorran Franc		\N
7	AON	Angolan New Kwanza		\N
8	ARS	Argentine Peso		\N
9	AMD	Armenian Dram		\N
10	AWG	Aruban Florin		\N
11	AUD	Australian Dollar		\N
12	AZM	Azerbaijan Manat		\N
13	BSD	Bahamanian Dollar		\N
14	BHD	Bahraini Dinar		\N
15	BDT	Bangladeshi Taka		\N
16	BBD	Barbados Dollar		\N
17	BYR	Belarus Ruble		\N
18	BZD	Belize Dollar		\N
19	BMD	Bermudian Dollar		\N
20	BTN	Bhutan Ngultrum		\N
21	BOB	Bolivian Boliviano		\N
22	BAM	Bosnia and Herzegovina Marka		\N
23	BWP	Botswana Pula		\N
24	BRL	Brazilian Real		\N
25	BND	Brunei Dollar		\N
26	BGL	Bulgarian Lev		\N
27	BIF	Burundi Franc		\N
28	KHR	Cambodian Riel		\N
29	CAD	Canadian Dollar		\N
30	BPS	Canton & Enderbury Island Pound		\N
31	CVE	Cape Verde Escudo		\N
32	KYD	Cayman Islands Dollar		\N
33	CFP	Central Pacific Franc		\N
34	XOF	CFA Franc BCEAO		\N
35	XAF	CFA Franc BEAC		\N
36	CLP	Chilean Peso		\N
37	CLF	Chilean U. Fomento		\N
38	CNY	Chinese Yuan Renminbi		\N
39	COP	Colombian Peso		\N
40	KMF	Comoros Franc		\N
41	CRC	Costa Rican Colon		\N
42	HRK	Croatian Kuna		\N
43	CUP	Cuban Peso		\N
44	CYP	Cyprus Pound		\N
46	DKK	Danish Krone		\N
47	DJF	Djibouti Franc		\N
48	DOP	Dominican R. Peso		\N
49	XCD	East Caribbean Dollar		\N
50	ECS	Ecuador Sucre		\N
51	EGP	Egyptian Pound		\N
52	SVC	El Salvador Colon		\N
53	ERN	Eritrea Nakfa		\N
54	EEK	Estonian Kroon		\N
55	ETB	Ethiopian Birr		\N
56	FKP	Falkland Islands Pound		\N
57	FJD	Fiji Dollar		\N
58	XPF	French Pacific Islands Franc		\N
59	GMD	Gambian Dalasi		\N
60	GEL	Georgian Lari		\N
61	GHC	Ghanaian Cedi		\N
62	GIP	Gibraltar Pound		\N
64	GTQ	Guatemalan Quetzal		\N
65	GGP	Guernsey Pound		\N
66	GNF	Guinea Franc		\N
67	GYD	Guyanese Dollar		\N
68	HTG	Haitian Gourde		\N
69	HNL	Honduran Lempira		\N
70	HKD	Hong Kong Dollar		\N
71	HUF	Hungarian Forint		\N
72	ISK	Iceland Krona		\N
73	INR	Indian Rupee		\N
74	IDR	Indonesian Rupiah		\N
75	IRR	Iranian Rial		\N
76	IQD	Iraqi Dinar		\N
77	IMP	Isle Of Man Pound		\N
78	ILS	Israeli New Shekel		\N
79	JMD	Jamaican Dollar		\N
80	JPY	Japanese Yen		\N
81	JEP	Jersey Pound		\N
82	JOD	Jordanian Dinar		\N
83	KZT	Kazakhstan Tenge		\N
84	KES	Kenyan Shilling		\N
85	KWD	Kuwaiti Dinar		\N
86	KGS	Kyrgyzstan Som		\N
87	LAK	Lao Kip		\N
88	LVL	Latvian Lats		\N
89	LBP	Lebanese Pound		\N
90	LSL	Lesotho Loti		\N
91	LRD	Liberian Dollar		\N
92	LYD	Libyan Dinar		\N
93	LTL	Lithuanian Litas		\N
94	MOP	Macau Pataca		\N
95	MKD	Macedonia Denar		\N
96	MGF	Malagasy Franc		\N
97	MWK	Malawi Kwacha		\N
98	MYR	Malaysian Ringgit		\N
99	MVR	Maldive Rufiyaa		\N
100	MTL	Maltese Lira		\N
101	MRO	Mauritanian Ouguiya		\N
102	MUR	Mauritius Rupee		\N
103	MXP	Mexican Peso		\N
104	UDI	Mexican Unidades De Inversion		\N
105	MDL	Moldova Leu		\N
106	MNT	Mongolian Tugrik		\N
107	MAD	Moroccan Dirham		\N
108	MZM	Mozambique Metical		\N
109	MMK	Myanmar Kyat		\N
110	NAD	Namibia Dollar		\N
111	NPR	Nepalese Rupee		\N
112	NZD	New Zealand Dollar		\N
113	NIO	Nicaraguan Cordoba Oro		\N
114	NGN	Nigerian Naira		\N
115	ANG	NL Antillian Guilder		\N
116	KPW	North Korean Won		\N
117	NOK	Norwegian Kroner		\N
118	OMR	Omani Rial		\N
119	PKR	Pakistan Rupee		\N
120	XPD	Palladium		\N
121	PAB	Panamanian Balboa		\N
122	PGK	Papua New Guinea Kina		\N
123	PYG	Paraguay Guarani		\N
124	PEN	Peruvian Nuevo Sol		\N
125	PHP	Philippine Peso		\N
126	XPT	Platinum		\N
127	PLN	Polish New Zloty		\N
128	QAR	Qatari Rial		\N
129	CDF	Republic of Congo Franc		\N
130	ROL	Romanian Leu		\N
131	RUB	Russian Rouble		\N
132	RWF	Rwanda Franc		\N
133	WST	Samoan Tala		\N
134	STD	Sao Tome/Principe Dobra		\N
135	SAR	Saudi Riyal		\N
136	SPL	Seborga Luigini		\N
137	SCR	Seychelles Rupee		\N
138	SLL	Sierra Leone Leone		\N
139	XAG	Silver		\N
140	SGD	Singapore Dollar		\N
141	SKK	Slovak Koruna		\N
142	SIT	Slovenian Tolar		\N
143	SBD	Solomon Islands Dollar		\N
144	SOS	Somali Shilling		\N
145	ZAR	South African Rand		\N
146	KRW	South Korean Won		\N
147	XDR	Special Drawing Right		\N
148	LKR	Sri Lanka Rupee		\N
149	SHP	St. Helena Pound		\N
150	SDD	Sudanese Dinar		\N
151	SDP	Sudanese Pound		\N
152	SRG	Suriname Guilder		\N
153	SZL	Swaziland Lilangeni		\N
154	SEK	Swedish Krona		\N
155	CHF	Swiss Franc		\N
156	SYP	Syrian Pound		\N
157	TWD	Taiwan Dollar		\N
158	TJS	Tajikistan Ruble		\N
159	TZS	Tanzanian Shilling		\N
160	THB	Thai Baht		\N
161	TOP	Tonga Paanga		\N
162	TTD	Trinidad/Tobago Dollar		\N
163	TND	Tunisian Dinar		\N
164	TRY	Turkish New Lira		\N
165	TMM	Turkmenistan Manat		\N
166	UGS	Uganda Shilling		\N
167	UAH	Ukraine Hryvnia		\N
168	UYP	Uruguayan Peso		\N
179	_RB	Rabbits		Rabbits
170	AED	Utd. Arab Emir. Dirham		\N
171	UZS	Uzbekistan Sum		\N
172	VUV	Vanuatu Vatu		\N
173	VEB	Venezuelan Bolivar		\N
174	VND	Vietnamese Dong		\N
175	YER	Yemeni Rial		\N
176	YUN	Yugoslav Dinar		\N
177	ZMK	Zambian Kwacha		\N
178	ZWD	Zimbabwe Dollar		\N
45	CZK	Czech Koruna		Kč
169	USD	US Dollar	$	\N
63	XAU	Gold		g.&nbsp;of&nbsp;Gold
\.


--
-- Name: international_currency_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('international_currency_id_seq', 179, true);


--
-- Data for Name: international_currency_rate; Type: TABLE DATA; Schema: public; Owner: -
--

COPY international_currency_rate (id, currency_code, currency_code_from, source, date, amount) FROM stdin;
\.


--
-- Name: international_currency_rate_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('international_currency_rate_id_seq', 172, true);


--
-- Data for Name: international_translation; Type: TABLE DATA; Schema: public; Owner: -
--

COPY international_translation (id, locale, original_string, translated_string, context, node_id) FROM stdin;
\.


--
-- Name: international_translation_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('international_translation_id_seq', 1, false);


--
-- Name: client_action_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY client_action
    ADD CONSTRAINT client_action_pkey PRIMARY KEY (id);


--
-- Name: client_address_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY client_address
    ADD CONSTRAINT client_address_pkey PRIMARY KEY (id);


--
-- Name: client_company_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY client_company
    ADD CONSTRAINT client_company_pkey PRIMARY KEY (id);


--
-- Name: client_customer_email_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY client_customer
    ADD CONSTRAINT client_customer_email_key UNIQUE (email, deleted_date);


--
-- Name: client_customer_group_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY client_customer_group
    ADD CONSTRAINT client_customer_group_pkey PRIMARY KEY (id);


--
-- Name: client_customer_image_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY client_customer_image
    ADD CONSTRAINT client_customer_image_pkey PRIMARY KEY (id);


--
-- Name: client_customer_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY client_customer
    ADD CONSTRAINT client_customer_pkey PRIMARY KEY (id);


--
-- Name: client_customer_role_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY client_customer_role
    ADD CONSTRAINT client_customer_role_pkey PRIMARY KEY (id);


--
-- Name: client_customer_taxonomy_node_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY client_customer_taxonomy
    ADD CONSTRAINT client_customer_taxonomy_node_id_key UNIQUE (node_id, taxonomy_tree_id);


--
-- Name: client_customer_taxonomy_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY client_customer_taxonomy
    ADD CONSTRAINT client_customer_taxonomy_pkey PRIMARY KEY (id);


--
-- Name: client_customer_token_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY client_customer_token
    ADD CONSTRAINT client_customer_token_pkey PRIMARY KEY (id);


--
-- Name: client_group_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY client_group
    ADD CONSTRAINT client_group_pkey PRIMARY KEY (id);


--
-- Name: client_role_permission_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY client_role_permission
    ADD CONSTRAINT client_role_permission_pkey PRIMARY KEY (id);


--
-- Name: client_role_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY client_role
    ADD CONSTRAINT client_role_pkey PRIMARY KEY (id);


--
-- Name: common_comment_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_comment
    ADD CONSTRAINT common_comment_pkey PRIMARY KEY (id);


--
-- Name: common_configuration_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_configuration
    ADD CONSTRAINT common_configuration_pkey PRIMARY KEY (id);


--
-- Name: common_email_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_email
    ADD CONSTRAINT common_email_pkey PRIMARY KEY (id);


--
-- Name: common_file_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_file
    ADD CONSTRAINT common_file_pkey PRIMARY KEY (id);


--
-- Name: common_image_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_image
    ADD CONSTRAINT common_image_pkey PRIMARY KEY (id);


--
-- Name: common_node_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_node
    ADD CONSTRAINT common_node_pkey PRIMARY KEY (id);


--
-- Name: common_node_taxonomy_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_node_taxonomy
    ADD CONSTRAINT common_node_taxonomy_pkey PRIMARY KEY (id);


--
-- Name: common_print_article_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_print_article
    ADD CONSTRAINT common_print_article_pkey PRIMARY KEY (id);


--
-- Name: common_revision_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_revision
    ADD CONSTRAINT common_revision_pkey PRIMARY KEY (id);


--
-- Name: common_scheduler_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_scheduler
    ADD CONSTRAINT common_scheduler_pkey PRIMARY KEY (id);


--
-- Name: common_session_archive_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_session_archive
    ADD CONSTRAINT common_session_archive_pkey PRIMARY KEY (id);


--
-- Name: common_session_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_session
    ADD CONSTRAINT common_session_pkey PRIMARY KEY (id);


--
-- Name: common_taxonomy_label_image_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_taxonomy_label_image
    ADD CONSTRAINT common_taxonomy_label_image_pkey PRIMARY KEY (id);


--
-- Name: common_taxonomy_label_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_taxonomy_label
    ADD CONSTRAINT common_taxonomy_label_pkey PRIMARY KEY (id);


--
-- Name: common_taxonomy_tree_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_taxonomy_tree
    ADD CONSTRAINT common_taxonomy_tree_pkey PRIMARY KEY (id);


--
-- Name: common_uri_mapping_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_uri_mapping
    ADD CONSTRAINT common_uri_mapping_pkey PRIMARY KEY (id);


--
-- Name: common_uri_mapping_public_uri_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_uri_mapping
    ADD CONSTRAINT common_uri_mapping_public_uri_key UNIQUE (public_uri);


--
-- Name: common_watchdog_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_watchdog
    ADD CONSTRAINT common_watchdog_pkey PRIMARY KEY (id);


--
-- Name: country_id_zone_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_delivery_carrier_zone_to_country
    ADD CONSTRAINT country_id_zone_id_key UNIQUE (country_id, zone_id);


--
-- Name: ecommerce_basket_content_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_basket_content
    ADD CONSTRAINT ecommerce_basket_content_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_basket_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_basket
    ADD CONSTRAINT ecommerce_basket_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_delivery_carrier_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_delivery_carrier
    ADD CONSTRAINT ecommerce_delivery_carrier_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_delivery_carrier_rate_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_delivery_carrier_rate
    ADD CONSTRAINT ecommerce_delivery_carrier_rate_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_delivery_carrier_zone_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_delivery_carrier_zone
    ADD CONSTRAINT ecommerce_delivery_carrier_zone_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_delivery_carrier_zone_to_country_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_delivery_carrier_zone_to_country
    ADD CONSTRAINT ecommerce_delivery_carrier_zone_to_country_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_delivery_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_delivery
    ADD CONSTRAINT ecommerce_delivery_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_invoice_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_invoice
    ADD CONSTRAINT ecommerce_invoice_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_offer_group_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_offer_group
    ADD CONSTRAINT ecommerce_offer_group_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_offer_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_offer
    ADD CONSTRAINT ecommerce_offer_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_order_log_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_order_log
    ADD CONSTRAINT ecommerce_order_log_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_order_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_order
    ADD CONSTRAINT ecommerce_order_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_price_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_price
    ADD CONSTRAINT ecommerce_price_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_product_image_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_product_image
    ADD CONSTRAINT ecommerce_product_image_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_product_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_product
    ADD CONSTRAINT ecommerce_product_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_product_review_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_product_review
    ADD CONSTRAINT ecommerce_product_review_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_product_taxonomy_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_product_taxonomy
    ADD CONSTRAINT ecommerce_product_taxonomy_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_product_to_product_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_product_to_product
    ADD CONSTRAINT ecommerce_product_to_product_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_product_type_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_product_type
    ADD CONSTRAINT ecommerce_product_type_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_product_variety_code_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_product_variety
    ADD CONSTRAINT ecommerce_product_variety_code_key UNIQUE (sku);


--
-- Name: ecommerce_product_variety_image_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_product_variety_image
    ADD CONSTRAINT ecommerce_product_variety_image_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_product_variety_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_product_variety
    ADD CONSTRAINT ecommerce_product_variety_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_product_variety_taxonomy_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_product_variety_taxonomy
    ADD CONSTRAINT ecommerce_product_variety_taxonomy_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_promotion_code_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_promotion_code
    ADD CONSTRAINT ecommerce_promotion_code_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_promotion_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_promotion
    ADD CONSTRAINT ecommerce_promotion_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_promotion_type_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_promotion_type
    ADD CONSTRAINT ecommerce_promotion_type_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_recipe_image_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_recipe_image
    ADD CONSTRAINT ecommerce_recipe_image_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_recipe_ingredients_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_recipe_ingredients
    ADD CONSTRAINT ecommerce_recipe_ingredients_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_recipe_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_recipe
    ADD CONSTRAINT ecommerce_recipe_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_recipe_review_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_recipe_review
    ADD CONSTRAINT ecommerce_recipe_review_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_recipe_taxonomy_node_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_recipe_taxonomy
    ADD CONSTRAINT ecommerce_recipe_taxonomy_node_id_key UNIQUE (node_id, taxonomy_tree_id);


--
-- Name: ecommerce_recipe_taxonomy_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_recipe_taxonomy
    ADD CONSTRAINT ecommerce_recipe_taxonomy_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_store_image_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_store_image
    ADD CONSTRAINT ecommerce_store_image_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_store_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_store
    ADD CONSTRAINT ecommerce_store_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_store_taxonomy_node_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_store_taxonomy
    ADD CONSTRAINT ecommerce_store_taxonomy_node_id_key UNIQUE (node_id, taxonomy_tree_id);


--
-- Name: ecommerce_store_taxonomy_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_store_taxonomy
    ADD CONSTRAINT ecommerce_store_taxonomy_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_store_type_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_store_type
    ADD CONSTRAINT ecommerce_store_type_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_transaction_order_id_pg_data_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_transaction
    ADD CONSTRAINT ecommerce_transaction_order_id_pg_data_key UNIQUE (order_id, pg_data);


--
-- Name: ecommerce_transaction_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_transaction
    ADD CONSTRAINT ecommerce_transaction_pkey PRIMARY KEY (id);


--
-- Name: education_survey_entry_answer_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY education_survey_entry_answer
    ADD CONSTRAINT education_survey_entry_answer_pkey PRIMARY KEY (id);


--
-- Name: education_survey_entry_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY education_survey_entry
    ADD CONSTRAINT education_survey_entry_pkey PRIMARY KEY (id);


--
-- Name: education_survey_entry_survey_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY education_survey_entry
    ADD CONSTRAINT education_survey_entry_survey_id_key UNIQUE (survey_id, customer_id, relation_subject);


--
-- Name: education_survey_image_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY education_survey_image
    ADD CONSTRAINT education_survey_image_pkey PRIMARY KEY (id);


--
-- Name: education_survey_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY education_survey
    ADD CONSTRAINT education_survey_pkey PRIMARY KEY (id);


--
-- Name: education_survey_question_answer_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY education_survey_question_answer
    ADD CONSTRAINT education_survey_question_answer_pkey PRIMARY KEY (id);


--
-- Name: education_survey_question_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY education_survey_question
    ADD CONSTRAINT education_survey_question_pkey PRIMARY KEY (id);


--
-- Name: international_country_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY international_country
    ADD CONSTRAINT international_country_pkey PRIMARY KEY (id);


--
-- Name: international_currency_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY international_currency
    ADD CONSTRAINT international_currency_pkey PRIMARY KEY (id);


--
-- Name: international_currency_rate_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY international_currency_rate
    ADD CONSTRAINT international_currency_rate_pkey PRIMARY KEY (id);


--
-- Name: international_translation_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY international_translation
    ADD CONSTRAINT international_translation_pkey PRIMARY KEY (id);


--
-- Name: node_node_id_taxonomy_tree_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY common_node_taxonomy
    ADD CONSTRAINT node_node_id_taxonomy_tree_id_key UNIQUE (node_id, taxonomy_tree_id);


--
-- Name: offer_group_id_product_variety_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_offer
    ADD CONSTRAINT offer_group_id_product_variety_id_key UNIQUE (offer_group_id, product_variety_id);


--
-- Name: product_id_related_product_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_product_to_product
    ADD CONSTRAINT product_id_related_product_id_key UNIQUE (product_id, related_product_id);


--
-- Name: product_node_id_taxonomy_tree_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_product_taxonomy
    ADD CONSTRAINT product_node_id_taxonomy_tree_id_key UNIQUE (node_id, taxonomy_tree_id);


--
-- Name: product_variety_node_id_taxonomy_tree_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_product_variety_taxonomy
    ADD CONSTRAINT product_variety_node_id_taxonomy_tree_id_key UNIQUE (node_id, taxonomy_tree_id);


--
-- Name: client_action_customer_id_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX client_action_customer_id_key ON client_action USING btree (customer_id);


--
-- Name: client_action_network_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX client_action_network_key ON client_action USING btree (network);


--
-- Name: client_address_country_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX client_address_country_id_idx ON client_address USING btree (country_id);


--
-- Name: client_address_customer_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX client_address_customer_id_idx ON client_address USING btree (customer_id);


--
-- Name: client_company_customer_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX client_company_customer_id_idx ON client_company USING btree (customer_id);


--
-- Name: client_customer_group_customer_id_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX client_customer_group_customer_id_key ON client_customer_group USING btree (customer_id);


--
-- Name: client_customer_group_group_id_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX client_customer_group_group_id_key ON client_customer_group USING btree (group_id);


--
-- Name: client_customer_image_node_id_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX client_customer_image_node_id_key ON client_customer_image USING btree (node_id);


--
-- Name: client_customer_role_customer_id_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX client_customer_role_customer_id_key ON client_customer_role USING btree (customer_id);


--
-- Name: client_customer_role_role_id_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX client_customer_role_role_id_key ON client_customer_role USING btree (role_id);


--
-- Name: client_customer_taxonomy_node_id_key1; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX client_customer_taxonomy_node_id_key1 ON client_customer_taxonomy USING btree (node_id);


--
-- Name: client_customer_taxonomy_taxonomy_tree_id_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX client_customer_taxonomy_taxonomy_tree_id_key ON client_customer_taxonomy USING btree (taxonomy_tree_id);


--
-- Name: client_customer_token_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX client_customer_token_key ON client_customer_token USING btree (token);


--
-- Name: client_customer_token_publish_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX client_customer_token_publish_key ON client_customer_token USING btree (publish);


--
-- Name: client_role_permission_role_id_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX client_role_permission_role_id_key ON client_role_permission USING btree (role_id);


--
-- Name: common_comment_costomer_id_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_comment_costomer_id_id_idx ON common_comment USING btree (customer_id);


--
-- Name: common_comment_node_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_comment_node_id_idx ON common_comment USING btree (node_id);


--
-- Name: common_comment_node_id_key1; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_comment_node_id_key1 ON common_comment USING btree (node_id);


--
-- Name: common_comment_parent_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_comment_parent_idx ON common_comment USING btree (parent);


--
-- Name: common_file_node_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_file_node_id_idx ON common_file USING btree (node_id);


--
-- Name: common_image_node_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_image_node_id_idx ON common_image USING btree (node_id);


--
-- Name: common_node_custom_fields_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_node_custom_fields_idx ON common_node USING gin (custom_fields);


--
-- Name: common_node_display_in_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_node_display_in_idx ON common_node USING btree (display_in_menu);


--
-- Name: common_node_node_controller_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_node_node_controller_idx ON common_node USING btree (node_controller);


--
-- Name: common_node_node_type_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_node_node_type_idx ON common_node USING btree (node_group);


--
-- Name: common_node_parent_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_node_parent_idx ON common_node USING btree (parent);


--
-- Name: common_node_publish_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_node_publish_idx ON common_node USING btree (publish);


--
-- Name: common_node_taxonomy_node_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_node_taxonomy_node_id_idx ON common_node_taxonomy USING btree (node_id);


--
-- Name: common_node_taxonomy_taxonomy_tree_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_node_taxonomy_taxonomy_tree_id_idx ON common_node_taxonomy USING btree (taxonomy_tree_id);


--
-- Name: common_print_article_node_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_print_article_node_id_idx ON common_print_article USING btree (node_id);


--
-- Name: common_revision_combined_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_revision_combined_idx ON common_revision USING btree (object, node_id);


--
-- Name: common_scheduler_lock_token_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_scheduler_lock_token_key ON common_scheduler USING btree (lock_token);


--
-- Name: common_scheduler_node_id_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_scheduler_node_id_key ON common_scheduler USING btree (node_id);


--
-- Name: common_scheduler_scheduled_time_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_scheduler_scheduled_time_key ON common_scheduler USING btree (scheduled_time);


--
-- Name: common_scheduler_status_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_scheduler_status_key ON common_scheduler USING btree (status);


--
-- Name: common_session_modified_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_session_modified_idx ON common_session USING btree (modified);


--
-- Name: common_session_session_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_session_session_id_idx ON common_session USING btree (session_id);


--
-- Name: common_taxonomy_tree_label_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_taxonomy_tree_label_id_idx ON common_taxonomy_tree USING btree (label_id);


--
-- Name: common_taxonomy_tree_parent_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_taxonomy_tree_parent_idx ON common_taxonomy_tree USING btree (parent);


--
-- Name: common_uri_mapping_node_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_uri_mapping_node_id_idx ON common_uri_mapping USING btree (node_id);


--
-- Name: common_watchdog_combined_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX common_watchdog_combined_idx ON common_watchdog USING btree (name, watched_item_id, publish);


--
-- Name: ecommerce_basket_content_basket_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_basket_content_basket_id_idx ON ecommerce_basket_content USING btree (basket_id);


--
-- Name: ecommerce_basket_content_price_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_basket_content_price_id_idx ON ecommerce_basket_content USING btree (price_id);


--
-- Name: ecommerce_basket_content_product_variety_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_basket_content_product_variety_id_idx ON ecommerce_basket_content USING btree (product_variety_id);


--
-- Name: ecommerce_basket_customer_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_basket_customer_id_idx ON ecommerce_basket USING btree (customer_id);


--
-- Name: ecommerce_delivery_carrier_rate_carrier_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_delivery_carrier_rate_carrier_id_idx ON ecommerce_delivery_carrier_rate USING btree (carrier_id);


--
-- Name: ecommerce_delivery_carrier_rate_weight_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_delivery_carrier_rate_weight_idx ON ecommerce_delivery_carrier_rate USING btree (weight_from, weight_to);


--
-- Name: ecommerce_invoice_order_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_invoice_order_id_idx ON ecommerce_invoice USING btree (order_id);


--
-- Name: ecommerce_order_basket_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_order_basket_id_idx ON ecommerce_order USING btree (basket_id);


--
-- Name: ecommerce_order_delivery_address_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_order_delivery_address_id_idx ON ecommerce_order USING btree (delivery_address_id);


--
-- Name: ecommerce_order_invoices_address_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_order_invoices_address_id_idx ON ecommerce_order USING btree (invoices_address_id);


--
-- Name: ecommerce_order_log_order_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_order_log_order_id_idx ON ecommerce_order_log USING btree (order_id);


--
-- Name: ecommerce_order_log_status_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_order_log_status_idx ON ecommerce_order_log USING btree (status);


--
-- Name: ecommerce_order_review_email_sent_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_order_review_email_sent_idx ON ecommerce_order USING btree (review_email_sent);


--
-- Name: ecommerce_price_currency_code_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_price_currency_code_idx ON ecommerce_price USING btree (currency_code);


--
-- Name: ecommerce_price_product_variety_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_price_product_variety_id_idx ON ecommerce_price USING btree (product_variety_id);


--
-- Name: ecommerce_price_type_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_price_type_idx ON ecommerce_price USING btree (type);


--
-- Name: ecommerce_product_image_node_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_product_image_node_id_idx ON ecommerce_product_image USING btree (node_id);


--
-- Name: ecommerce_product_publish_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_product_publish_idx ON ecommerce_product USING btree (publish);


--
-- Name: ecommerce_product_review_node_id_key1; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_product_review_node_id_key1 ON ecommerce_product_review USING btree (node_id);


--
-- Name: ecommerce_product_taxonomy_node_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_product_taxonomy_node_id_idx ON ecommerce_product_taxonomy USING btree (node_id);


--
-- Name: ecommerce_product_taxonomy_taxonomy_tree_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_product_taxonomy_taxonomy_tree_id_idx ON ecommerce_product_taxonomy USING btree (taxonomy_tree_id);


--
-- Name: ecommerce_product_to_product_product_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_product_to_product_product_id_idx ON ecommerce_product_to_product USING btree (product_id);


--
-- Name: ecommerce_product_to_product_related_product_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_product_to_product_related_product_id_idx ON ecommerce_product_to_product USING btree (related_product_id);


--
-- Name: ecommerce_product_variety_image_node_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_product_variety_image_node_id_idx ON ecommerce_product_variety_image USING btree (node_id);


--
-- Name: ecommerce_product_variety_product_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_product_variety_product_id_idx ON ecommerce_product_variety USING btree (product_id);


--
-- Name: ecommerce_product_variety_taxonomy_node_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_product_variety_taxonomy_node_id_idx ON ecommerce_product_variety_taxonomy USING btree (node_id);


--
-- Name: ecommerce_product_variety_taxonomy_taxonomy_tree_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_product_variety_taxonomy_taxonomy_tree_id_idx ON ecommerce_product_variety_taxonomy USING btree (taxonomy_tree_id);


--
-- Name: ecommerce_recipe_image_node_id_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_recipe_image_node_id_key ON ecommerce_recipe_image USING btree (node_id);


--
-- Name: ecommerce_recipe_review_node_id_key1; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_recipe_review_node_id_key1 ON ecommerce_recipe_review USING btree (node_id);


--
-- Name: ecommerce_recipe_taxonomy_node_id_key1; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_recipe_taxonomy_node_id_key1 ON ecommerce_recipe_taxonomy USING btree (node_id);


--
-- Name: ecommerce_recipe_taxonomy_taxonomy_tree_id_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_recipe_taxonomy_taxonomy_tree_id_key ON ecommerce_recipe_taxonomy USING btree (taxonomy_tree_id);


--
-- Name: ecommerce_store_image_node_id_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_store_image_node_id_key ON ecommerce_store_image USING btree (node_id);


--
-- Name: ecommerce_store_taxonomy_node_id_key1; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_store_taxonomy_node_id_key1 ON ecommerce_store_taxonomy USING btree (node_id);


--
-- Name: ecommerce_store_taxonomy_taxonomy_tree_id_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_store_taxonomy_taxonomy_tree_id_key ON ecommerce_store_taxonomy USING btree (taxonomy_tree_id);


--
-- Name: ecommerce_transaction_order_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ecommerce_transaction_order_id_idx ON ecommerce_transaction USING btree (order_id);


--
-- Name: education_survey_image_node_id_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX education_survey_image_node_id_key ON education_survey_image USING btree (node_id);


--
-- Name: international_translation_locale_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX international_translation_locale_idx ON international_translation USING btree (locale);


--
-- Name: international_translation_node_id_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX international_translation_node_id_idx ON international_translation USING btree (node_id);


--
-- Name: client_action_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_action
    ADD CONSTRAINT client_action_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: client_action_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_action
    ADD CONSTRAINT client_action_node_id_fkey FOREIGN KEY (node_id) REFERENCES common_node(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: client_address_country_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_address
    ADD CONSTRAINT client_address_country_id_fkey FOREIGN KEY (country_id) REFERENCES international_country(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: client_address_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_address
    ADD CONSTRAINT client_address_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: client_company_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_company
    ADD CONSTRAINT client_company_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: client_customer_group_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_customer_group
    ADD CONSTRAINT client_customer_group_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: client_customer_group_group_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_customer_group
    ADD CONSTRAINT client_customer_group_group_id_fkey FOREIGN KEY (group_id) REFERENCES client_group(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: client_customer_image_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_customer_image
    ADD CONSTRAINT client_customer_image_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: client_customer_image_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_customer_image
    ADD CONSTRAINT client_customer_image_node_id_fkey FOREIGN KEY (node_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: client_customer_role_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_customer_role
    ADD CONSTRAINT client_customer_role_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: client_customer_role_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_customer_role
    ADD CONSTRAINT client_customer_role_role_id_fkey FOREIGN KEY (role_id) REFERENCES client_role(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: client_customer_store_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_customer
    ADD CONSTRAINT client_customer_store_id_fkey FOREIGN KEY (store_id) REFERENCES ecommerce_store(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: client_customer_taxonomy_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_customer_taxonomy
    ADD CONSTRAINT client_customer_taxonomy_node_id_fkey FOREIGN KEY (node_id) REFERENCES client_customer(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: client_customer_taxonomy_taxonomy_tree_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_customer_taxonomy
    ADD CONSTRAINT client_customer_taxonomy_taxonomy_tree_id_fkey FOREIGN KEY (taxonomy_tree_id) REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: client_customer_token_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_customer_token
    ADD CONSTRAINT client_customer_token_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: client_role_permission_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY client_role_permission
    ADD CONSTRAINT client_role_permission_role_id_fkey FOREIGN KEY (role_id) REFERENCES client_role(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: common_comment_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_comment
    ADD CONSTRAINT common_comment_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: common_comment_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_comment
    ADD CONSTRAINT common_comment_node_id_fkey FOREIGN KEY (node_id) REFERENCES common_node(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: common_comment_parent_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_comment
    ADD CONSTRAINT common_comment_parent_fkey FOREIGN KEY (parent) REFERENCES common_comment(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: common_configuration_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_configuration
    ADD CONSTRAINT common_configuration_node_id_fkey FOREIGN KEY (node_id) REFERENCES common_node(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: common_file_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_file
    ADD CONSTRAINT common_file_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: common_file_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_file
    ADD CONSTRAINT common_file_node_id_fkey FOREIGN KEY (node_id) REFERENCES common_node(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: common_image_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_image
    ADD CONSTRAINT common_image_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: common_image_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_image
    ADD CONSTRAINT common_image_node_id_fkey FOREIGN KEY (node_id) REFERENCES common_node(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: common_node_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_node
    ADD CONSTRAINT common_node_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: common_node_link_to_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_node
    ADD CONSTRAINT common_node_link_to_node_id_fkey FOREIGN KEY (link_to_node_id) REFERENCES common_node(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: common_node_parent_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_node
    ADD CONSTRAINT common_node_parent_fkey FOREIGN KEY (parent) REFERENCES common_node(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: common_node_taxonomy_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_node_taxonomy
    ADD CONSTRAINT common_node_taxonomy_node_id_fkey FOREIGN KEY (node_id) REFERENCES common_node(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: common_node_taxonomy_taxonomy_tree_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_node_taxonomy
    ADD CONSTRAINT common_node_taxonomy_taxonomy_tree_id_fkey FOREIGN KEY (taxonomy_tree_id) REFERENCES common_taxonomy_tree(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: common_print_article_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_print_article
    ADD CONSTRAINT common_print_article_node_id_fkey FOREIGN KEY (node_id) REFERENCES common_node(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: common_revision_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_revision
    ADD CONSTRAINT common_revision_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: common_session_archive_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_session_archive
    ADD CONSTRAINT common_session_archive_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: common_session_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_session
    ADD CONSTRAINT common_session_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: common_taxonomy_label_image_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_taxonomy_label_image
    ADD CONSTRAINT common_taxonomy_label_image_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: common_taxonomy_label_image_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_taxonomy_label_image
    ADD CONSTRAINT common_taxonomy_label_image_node_id_fkey FOREIGN KEY (node_id) REFERENCES common_taxonomy_label(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: common_taxonomy_tree_label_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_taxonomy_tree
    ADD CONSTRAINT common_taxonomy_tree_label_id_fkey FOREIGN KEY (label_id) REFERENCES common_taxonomy_label(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: common_taxonomy_tree_parent_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_taxonomy_tree
    ADD CONSTRAINT common_taxonomy_tree_parent_fkey FOREIGN KEY (parent) REFERENCES common_taxonomy_tree(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: common_uri_mapping_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_uri_mapping
    ADD CONSTRAINT common_uri_mapping_node_id_fkey FOREIGN KEY (node_id) REFERENCES common_node(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: common_watchdog_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY common_watchdog
    ADD CONSTRAINT common_watchdog_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_basket_content_basket_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_basket_content
    ADD CONSTRAINT ecommerce_basket_content_basket_id_fkey FOREIGN KEY (basket_id) REFERENCES ecommerce_basket(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_basket_content_price_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_basket_content
    ADD CONSTRAINT ecommerce_basket_content_price_id_fkey FOREIGN KEY (price_id) REFERENCES ecommerce_price(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ecommerce_basket_content_product_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_basket_content
    ADD CONSTRAINT ecommerce_basket_content_product_type_id_fkey FOREIGN KEY (product_type_id) REFERENCES ecommerce_product_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_basket_content_product_variety_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_basket_content
    ADD CONSTRAINT ecommerce_basket_content_product_variety_id_fkey FOREIGN KEY (product_variety_id) REFERENCES ecommerce_product_variety(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_basket_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_basket
    ADD CONSTRAINT ecommerce_basket_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_delivery_carrier_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_delivery
    ADD CONSTRAINT ecommerce_delivery_carrier_id_fkey FOREIGN KEY (carrier_id) REFERENCES ecommerce_delivery_carrier(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_delivery_carrier_rate_carrier_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_delivery_carrier_rate
    ADD CONSTRAINT ecommerce_delivery_carrier_rate_carrier_id_fkey FOREIGN KEY (carrier_id) REFERENCES ecommerce_delivery_carrier(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_delivery_carrier_zone_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_delivery_carrier
    ADD CONSTRAINT ecommerce_delivery_carrier_zone_id_fkey FOREIGN KEY (zone_id) REFERENCES ecommerce_delivery_carrier_zone(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_delivery_carrier_zone_to_country_country_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_delivery_carrier_zone_to_country
    ADD CONSTRAINT ecommerce_delivery_carrier_zone_to_country_country_id_fkey FOREIGN KEY (country_id) REFERENCES international_country(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_delivery_carrier_zone_to_country_zone_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_delivery_carrier_zone_to_country
    ADD CONSTRAINT ecommerce_delivery_carrier_zone_to_country_zone_id_fkey FOREIGN KEY (zone_id) REFERENCES ecommerce_delivery_carrier_zone(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_delivery_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_delivery
    ADD CONSTRAINT ecommerce_delivery_order_id_fkey FOREIGN KEY (order_id) REFERENCES ecommerce_order(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_invoice_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_invoice
    ADD CONSTRAINT ecommerce_invoice_order_id_fkey FOREIGN KEY (order_id) REFERENCES ecommerce_order(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ecommerce_offer_campaign_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_offer
    ADD CONSTRAINT ecommerce_offer_campaign_category_id_fkey FOREIGN KEY (campaign_category_id) REFERENCES common_taxonomy_tree(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_offer_offer_group_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_offer
    ADD CONSTRAINT ecommerce_offer_offer_group_id_fkey FOREIGN KEY (offer_group_id) REFERENCES ecommerce_offer_group(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_offer_price_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_offer
    ADD CONSTRAINT ecommerce_offer_price_id_fkey FOREIGN KEY (price_id) REFERENCES ecommerce_price(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_offer_product_variety_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_offer
    ADD CONSTRAINT ecommerce_offer_product_variety_id_fkey FOREIGN KEY (product_variety_id) REFERENCES ecommerce_product_variety(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_offer_roundel_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_offer
    ADD CONSTRAINT ecommerce_offer_roundel_category_id_fkey FOREIGN KEY (roundel_category_id) REFERENCES common_taxonomy_tree(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_order_basket_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_order
    ADD CONSTRAINT ecommerce_order_basket_id_fkey FOREIGN KEY (basket_id) REFERENCES ecommerce_basket(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_order_delivery_address_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_order
    ADD CONSTRAINT ecommerce_order_delivery_address_id_fkey FOREIGN KEY (delivery_address_id) REFERENCES client_address(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_order_invoices_address_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_order
    ADD CONSTRAINT ecommerce_order_invoices_address_id_fkey FOREIGN KEY (invoices_address_id) REFERENCES client_address(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_order_log_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_order_log
    ADD CONSTRAINT ecommerce_order_log_order_id_fkey FOREIGN KEY (order_id) REFERENCES ecommerce_order(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_price_product_variety_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_price
    ADD CONSTRAINT ecommerce_price_product_variety_id_fkey FOREIGN KEY (product_variety_id) REFERENCES ecommerce_product_variety(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_product_image_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_image
    ADD CONSTRAINT ecommerce_product_image_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_product_image_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_image
    ADD CONSTRAINT ecommerce_product_image_node_id_fkey FOREIGN KEY (node_id) REFERENCES ecommerce_product(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_product_review_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_review
    ADD CONSTRAINT ecommerce_product_review_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_product_review_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_review
    ADD CONSTRAINT ecommerce_product_review_node_id_fkey FOREIGN KEY (node_id) REFERENCES ecommerce_product(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_product_review_parent_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_review
    ADD CONSTRAINT ecommerce_product_review_parent_fkey FOREIGN KEY (parent) REFERENCES ecommerce_product_review(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_product_taxonomy_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_taxonomy
    ADD CONSTRAINT ecommerce_product_taxonomy_node_id_fkey FOREIGN KEY (node_id) REFERENCES ecommerce_product(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_product_taxonomy_taxonomy_tree_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_taxonomy
    ADD CONSTRAINT ecommerce_product_taxonomy_taxonomy_tree_id_fkey FOREIGN KEY (taxonomy_tree_id) REFERENCES common_taxonomy_tree(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_product_to_product_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_to_product
    ADD CONSTRAINT ecommerce_product_to_product_product_id_fkey FOREIGN KEY (product_id) REFERENCES ecommerce_product(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_product_to_product_related_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_to_product
    ADD CONSTRAINT ecommerce_product_to_product_related_product_id_fkey FOREIGN KEY (related_product_id) REFERENCES ecommerce_product(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_product_variety_image_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_variety_image
    ADD CONSTRAINT ecommerce_product_variety_image_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_product_variety_image_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_variety_image
    ADD CONSTRAINT ecommerce_product_variety_image_node_id_fkey FOREIGN KEY (node_id) REFERENCES ecommerce_product_variety(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_product_variety_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_variety
    ADD CONSTRAINT ecommerce_product_variety_product_id_fkey FOREIGN KEY (product_id) REFERENCES ecommerce_product(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_product_variety_product_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_variety
    ADD CONSTRAINT ecommerce_product_variety_product_type_id_fkey FOREIGN KEY (product_type_id) REFERENCES ecommerce_product_type(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_product_variety_taxonomy_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_variety_taxonomy
    ADD CONSTRAINT ecommerce_product_variety_taxonomy_node_id_fkey FOREIGN KEY (node_id) REFERENCES ecommerce_product_variety(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_product_variety_taxonomy_taxonomy_tree_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_product_variety_taxonomy
    ADD CONSTRAINT ecommerce_product_variety_taxonomy_taxonomy_tree_id_fkey FOREIGN KEY (taxonomy_tree_id) REFERENCES common_taxonomy_tree(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_promotion_code_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_promotion_code
    ADD CONSTRAINT ecommerce_promotion_code_order_id_fkey FOREIGN KEY (order_id) REFERENCES ecommerce_order(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_promotion_code_promotion_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_promotion_code
    ADD CONSTRAINT ecommerce_promotion_code_promotion_id_fkey FOREIGN KEY (promotion_id) REFERENCES ecommerce_promotion(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_promotion_generated_by_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_promotion
    ADD CONSTRAINT ecommerce_promotion_generated_by_customer_id_fkey FOREIGN KEY (generated_by_customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_promotion_generated_by_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_promotion
    ADD CONSTRAINT ecommerce_promotion_generated_by_order_id_fkey FOREIGN KEY (generated_by_order_id) REFERENCES ecommerce_order(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_promotion_limit_by_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_promotion
    ADD CONSTRAINT ecommerce_promotion_limit_by_customer_id_fkey FOREIGN KEY (limit_by_customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_promotion_type_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_promotion
    ADD CONSTRAINT ecommerce_promotion_type_fkey FOREIGN KEY (type) REFERENCES ecommerce_promotion_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_recipe_image_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_recipe_image
    ADD CONSTRAINT ecommerce_recipe_image_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_recipe_image_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_recipe_image
    ADD CONSTRAINT ecommerce_recipe_image_node_id_fkey FOREIGN KEY (node_id) REFERENCES ecommerce_recipe(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_recipe_ingredients_product_variety_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_recipe_ingredients
    ADD CONSTRAINT ecommerce_recipe_ingredients_product_variety_id_fkey FOREIGN KEY (product_variety_id) REFERENCES ecommerce_product_variety(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ecommerce_recipe_ingredients_recipe_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_recipe_ingredients
    ADD CONSTRAINT ecommerce_recipe_ingredients_recipe_id_fkey FOREIGN KEY (recipe_id) REFERENCES ecommerce_recipe(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ecommerce_recipe_ingredients_units_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_recipe_ingredients
    ADD CONSTRAINT ecommerce_recipe_ingredients_units_fkey FOREIGN KEY (units) REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ecommerce_recipe_review_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_recipe_review
    ADD CONSTRAINT ecommerce_recipe_review_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_recipe_review_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_recipe_review
    ADD CONSTRAINT ecommerce_recipe_review_node_id_fkey FOREIGN KEY (node_id) REFERENCES ecommerce_recipe(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_recipe_review_parent_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_recipe_review
    ADD CONSTRAINT ecommerce_recipe_review_parent_fkey FOREIGN KEY (parent) REFERENCES ecommerce_recipe_review(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_recipe_taxonomy_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_recipe_taxonomy
    ADD CONSTRAINT ecommerce_recipe_taxonomy_node_id_fkey FOREIGN KEY (node_id) REFERENCES ecommerce_recipe(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ecommerce_recipe_taxonomy_taxonomy_tree_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_recipe_taxonomy
    ADD CONSTRAINT ecommerce_recipe_taxonomy_taxonomy_tree_id_fkey FOREIGN KEY (taxonomy_tree_id) REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ecommerce_store_country_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_store
    ADD CONSTRAINT ecommerce_store_country_id_fkey FOREIGN KEY (country_id) REFERENCES international_country(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_store_image_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_store_image
    ADD CONSTRAINT ecommerce_store_image_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_store_image_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_store_image
    ADD CONSTRAINT ecommerce_store_image_node_id_fkey FOREIGN KEY (node_id) REFERENCES ecommerce_store(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ecommerce_store_taxonomy_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_store_taxonomy
    ADD CONSTRAINT ecommerce_store_taxonomy_node_id_fkey FOREIGN KEY (node_id) REFERENCES ecommerce_store(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ecommerce_store_taxonomy_taxonomy_tree_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_store_taxonomy
    ADD CONSTRAINT ecommerce_store_taxonomy_taxonomy_tree_id_fkey FOREIGN KEY (taxonomy_tree_id) REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ecommerce_store_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_store
    ADD CONSTRAINT ecommerce_store_type_id_fkey FOREIGN KEY (type_id) REFERENCES ecommerce_store_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: ecommerce_transaction_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_transaction
    ADD CONSTRAINT ecommerce_transaction_order_id_fkey FOREIGN KEY (order_id) REFERENCES ecommerce_order(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: education_survey_entry_answer_question_answer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY education_survey_entry_answer
    ADD CONSTRAINT education_survey_entry_answer_question_answer_id_fkey FOREIGN KEY (question_answer_id) REFERENCES education_survey_question_answer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: education_survey_entry_answer_question_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY education_survey_entry_answer
    ADD CONSTRAINT education_survey_entry_answer_question_id_fkey FOREIGN KEY (question_id) REFERENCES education_survey_question(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: education_survey_entry_answer_survey_entry_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY education_survey_entry_answer
    ADD CONSTRAINT education_survey_entry_answer_survey_entry_id_fkey FOREIGN KEY (survey_entry_id) REFERENCES education_survey_entry(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: education_survey_entry_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY education_survey_entry
    ADD CONSTRAINT education_survey_entry_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: education_survey_entry_survey_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY education_survey_entry
    ADD CONSTRAINT education_survey_entry_survey_id_fkey FOREIGN KEY (survey_id) REFERENCES education_survey(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: education_survey_image_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY education_survey_image
    ADD CONSTRAINT education_survey_image_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: education_survey_image_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY education_survey_image
    ADD CONSTRAINT education_survey_image_node_id_fkey FOREIGN KEY (node_id) REFERENCES education_survey(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: education_survey_question_answer_question_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY education_survey_question_answer
    ADD CONSTRAINT education_survey_question_answer_question_id_fkey FOREIGN KEY (question_id) REFERENCES education_survey_question(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: education_survey_question_parent_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY education_survey_question
    ADD CONSTRAINT education_survey_question_parent_fkey FOREIGN KEY (parent) REFERENCES education_survey_question(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: education_survey_question_survey_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY education_survey_question
    ADD CONSTRAINT education_survey_question_survey_id_fkey FOREIGN KEY (survey_id) REFERENCES education_survey(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: international_translation_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY international_translation
    ADD CONSTRAINT international_translation_node_id_fkey FOREIGN KEY (node_id) REFERENCES common_node(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: -
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

