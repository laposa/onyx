--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
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
    facebook_id bigint,
    twitter_id bigint,
    google_id bigint,
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
    apply_to_children smallint DEFAULT 0::smallint
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
    teaser text,
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
    customer_id integer
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
    limit_list_countries text,
    limit_list_products text,
    limit_list_product_types text,
    limit_order_value numeric(12,5) DEFAULT 0 NOT NULL,
    fixed_value numeric(12,5) DEFAULT 0 NOT NULL,
    fixed_percentage numeric(5,2) DEFAULT 0 NOT NULL,
    priority smallint DEFAULT 0 NOT NULL,
    publish smallint DEFAULT 1 NOT NULL,
    free_delivery_map text
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
    name character varying(255),
    carrier_id integer DEFAULT 1 NOT NULL
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
-- Name: ecommerce_delivery_carrier_zone_price; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ecommerce_delivery_carrier_zone_price (
    id integer NOT NULL,
    zone_id integer NOT NULL,
    weight integer,
    price numeric(9,2),
    currency_code character(3)
);


--
-- Name: ecommerce_delivery_carrier_zone_price_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ecommerce_delivery_carrier_zone_price_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ecommerce_delivery_carrier_zone_price_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE ecommerce_delivery_carrier_zone_price_id_seq OWNED BY ecommerce_delivery_carrier_zone_price.id;


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

ALTER TABLE ONLY ecommerce_delivery_carrier_zone_price ALTER COLUMN id SET DEFAULT nextval('ecommerce_delivery_carrier_zone_price_id_seq'::regclass);


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
1	Mr	Onxshop	Tester	\N	test@onxshop.com	\N	+44(0) 2890 328 988	\N	\N	b3f61bf1cb26243ef478a3c181dd0aa2	0	1	1	\N	2011-12-13 14:00:00	GBP	1	0	\N		2011-12-13 14:00:00	0	0	0	\N	\N	\N	\N	\N	\N	\N	\N
0		Anonym	Anonymouse		anonym@noemail.noemail	anonymouse	notelephone			9ce21d8f3992d89a325aa9dcf520a591	0	1	1	 	2011-12-13 14:00:00	GBP	0	0	2007-06-14		2011-12-13 14:00:00	0	0	0	\N	\N	\N	\N	\N	\N	\N	\N
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
1	1	_all_	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
2	2	front_office	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
3	2	nodes	edit	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
4	2	media	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
5	3	front_office	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
6	3	back_office	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
7	3	nodes	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
8	3	comments	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
9	3	surveys	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
10	3	media	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
11	3	taxonomy	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
12	3	seo_manager	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
13	3	scheduler	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
14	3	search_index	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
15	4	products	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
16	4	recipes	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
17	4	stores	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
18	4	orders	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
19	4	stock	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
20	4	customers	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
21	4	reports	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
22	4	discounts	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
23	4	currency	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
24	5	back_office	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
25	5	customers	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
26	5	orders	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
27	5	comments	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
28	5	surveys	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
29	5	discounts	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
30	6	stock	_all_	\N	2014-12-31 20:29:08.138829	2014-12-31 20:29:08.138829	\N
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
1	0	global	title	White Label		0
2	0	global	author_content	White Label, http://www.example.com/		0
5	0	global	locale	en_GB.UTF-8		0
6	0	global	default_currency	GBP		0
9	0	global	google_analytics			0
8	0	global	css	/**\r\n *\r\n * Our hint to CSS developers: \r\n * use here an @import of a CSS file from your own server,\r\n * work on your local version and paste here the final version \r\n * when you are finished with the development\r\n *\r\n */\r\n \r\n@import url(/share/css/default/theme_colour/grey.css);\r\n/*@import url(/share/css/default/theme_layout/stripes.css);*/		0
4	0	global	html_title_suffix	- White Label		0
10	0	global	google_adwords			0
11	0	global	display_content_side	1		0
12	0	global	extra_head	<meta name="viewport" content="width=1024" />		0
13	0	global	extra_body_top			0
14	0	global	extra_body_bottom			0
15	0	global	display_secondary_navigation	0		0
16	0	global	display_content_foot	0		0
17	5	global	html_title_suffix			0
3	0	global	credit	<a href="http://onxshop.com" title="Easy web CMS/eCommerce"><span>Powered by Onxshop</span></a>		0
7	0	global	admin_email	test@onxshop.com		0
\.


--
-- Name: common_configuration_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_configuration_id_seq', 17, true);


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
1	var/files/favicon.ico	main	3	Favicon		0	2011-12-13 14:27:55	1000	\N	\N	\N	\N
\.


--
-- Name: common_image_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_image_id_seq', 1, true);


--
-- Data for Name: common_node; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_node (id, title, node_group, node_controller, parent, parent_container, priority, teaser, content, description, keywords, page_title, head, created, modified, publish, display_in_menu, author, uri_title, display_permission, other_data, css_class, layout_style, component, relations, display_title, display_secondary_navigation, require_login, display_breadcrumb, browser_title, link_to_node_id, require_ssl, display_permission_group_acl, share_counter, customer_id) FROM stdin;
65	Payment was successfull	content	RTE	12	1	0	\N	<p>Process executed without error and the transaction was successfully Authorised.&nbsp;</p>			\N		2006-09-30 15:43:50	2008-08-24 18:27:47	1	1	1000		0	N;			N;	N;	0	\N	\N	0		0	0	\N	0	\N
89	Select Delivery Method	content	component	7	1	100	\N	\N					2010-04-18 01:34:49	2010-04-18 11:10:57	1	1	1000		0	N;			a:3:{s:8:"template";s:30:"ecommerce/delivery_option.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	1	\N	\N	0		0	0	\N	0	\N
91	Newsletter Subscribe	content	component	90	1	0	\N	\N					2010-04-18 11:20:58	2010-04-18 11:21:14	1	1	1000		0	N;			a:3:{s:8:"template";s:32:"client/newsletter_subscribe.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
93	Newsletter Unsubscribe	content	component	92	1	0	\N	\N					2010-04-18 11:22:40	2010-04-18 11:22:56	1	1	1000		0	N;			a:3:{s:8:"template";s:34:"client/newsletter_unsubscribe.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
1028	Other points	content	RTE	1026	1	0	\N	<p>This site has been designed with style sheets to allow maximum flexibility. Header tags and tables summaries have been effectively added to this site.</p>\n<p>Form controls are properly grouped and labelled.</p>					2010-04-18 13:03:47	2010-04-18 13:04:09	1	1	1000		0	N;			N;	N;	1	\N	\N	0		0	0	\N	0	\N
1029	Your feedback is important	content	RTE	1026	1	0	\N	<p>If you experience any problems using this site, or have some feedback or suggestions on how to improve accessibility, please <a class="internal-link" href="/page/20">contact us</a>.</p>					2010-04-18 13:04:34	2010-04-18 13:04:55	1	1	1000		0	N;			N;	N;	1	\N	\N	0		0	0	\N	0	\N
1030	More information	content	RTE	1026	1	0	\N	<p>For more information about website accessibility please visit the <a href="http://www.w3.org/TR/WCAG10/">W3C Web Content Accessibility Guidelines</a>.</p>					2010-04-18 13:05:16	2010-04-18 13:05:34	1	1	1000		0	N;			N;	N;	1	\N	\N	0		0	0	\N	0	\N
1017	Returns policy	content	RTE	26	1	10	\N	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\n<ul>\n<li>velit esse cillum dolore</li>\n<li>consectetur adipisicing elit</li>\n<li>occaecat cupidatat non proident</li>\n</ul>\n<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>					2008-08-16 13:01:53	2010-04-18 12:50:54	0	1	1000		0	N;			N;	N;	1	\N	\N	0		0	0	\N	0	\N
69	Search result	content	component	21	1	0	\N				\N		2006-09-30 15:49:27	2008-08-07 01:21:51	1	1	1000		0	N;			a:3:{s:8:"template";s:17:"search_nodes.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
72	Sitemap component	content	component	22	1	0	\N				\N		2006-09-30 15:50:21	2008-08-24 00:51:29	1	1	1000		0	N;			a:3:{s:8:"template";s:12:"sitemap.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
86	General content 1	content	RTE	85	0	0	\N	<p>Contact address,<br />Street Name,<br />CITY, Post Code<br />Tel: 01234 567 890</p>					2006-09-30 15:50:10	2010-04-18 13:07:36	1	1	1000		0	N;			N;	N;	0	\N	\N	0		0	0	\N	0	\N
68	Search input	content	component	21	1	0	\N				\N		2006-09-30 15:48:45	2008-08-24 18:22:11	1	1	1000		0	N;			a:3:{s:8:"template";s:14:"searchbox.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
32	Existing customer	content	component	8	1	0	\N				\N		2006-09-30 14:00:05	2008-08-24 01:15:22	1	1	1000		0	N;			a:3:{s:8:"template";s:17:"client/login.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
36	Registration component	content	component	13	1	0	\N				\N		2006-09-30 14:26:09	2008-08-24 01:14:57	1	1	1000		0	N;			a:3:{s:8:"template";s:24:"client/registration.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
34	New customer	content	component	8	1	0	\N				\N		2006-09-30 14:01:50	2008-08-24 01:15:34	1	1	1000		0	N;			a:3:{s:8:"template";s:30:"client/registration_start.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
75	Basket edit component	content	component	6	1	0	\N				\N		2006-09-30 15:54:35	2008-08-24 18:23:16	1	1	1000		0	N;			a:3:{s:8:"template";s:26:"ecommerce/basket_edit.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
41	Checkout	content	component	7	1	0	\N				\N		2006-09-30 14:47:01	2008-08-24 18:23:33	1	1	1000		0	N;			a:3:{s:8:"template";s:23:"ecommerce/checkout.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
39	Checkout Basket	content	component	7	1	0	\N				\N		2006-09-30 14:44:34	2008-08-24 18:23:51	1	1	1000		0	N;			a:3:{s:8:"template";s:30:"ecommerce/checkout_basket.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
0	Root	site	default	\N	0	0							2008-08-06 21:24:09	2008-08-06 21:24:09	1	1	0		0				\N	\N	\N	\N	\N	0		0	0	\N	0	\N
51	Order detail component	content	component	19	1	0	\N				\N		2006-09-30 15:22:49	2008-08-24 18:25:32	1	1	1000		0	N;			a:3:{s:8:"template";s:27:"ecommerce/order_detail.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
60	Payment component	content	component	10	1	0	\N				\N		2006-09-30 15:32:26	2008-08-24 18:26:22	1	1	1000		0	N;			a:3:{s:8:"template";s:22:"ecommerce/payment.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
1015	Our latest news	content	news_list	83	1	0	\N	\N			\N		2008-08-16 04:02:19	2011-01-16 15:54:36	1	1	1000		0	N;			a:5:{s:5:"limit";s:1:"5";s:8:"template";s:4:"full";s:10:"pagination";i:1;s:5:"image";i:0;s:13:"display_title";i:0;}	N;	0	\N	0	0		0	0	\N	0	\N
78	404 error	content	RTE	14	1	0	\N	<p><strong>We have recently restructured this website, you might find what you are looking for by going via the <a href="/">home page</a>.</strong></p>\r\n<p><strong>If you believe you have found a broken link please <a href="/page/20">let us know</a>.</strong></p>\r\n<div class="line">\r\n<hr />\r\n</div>\r\n<p><strong>Please try the following:</strong></p>\r\n<ul>\r\n<li>If you typed the page address in the Address bar, make sure that it is spelled correctly. </li>\r\n<li>Click the <a href="javascript:history.go(-1)">Back</a> button to try another link. </li>\r\n</ul>\r\n<p>HTTP 404 : Page not found</p>			\N		2006-09-30 16:37:05	2008-08-24 18:28:28	1	1	1000		0	N;			N;	N;	1	\N	\N	0		0	0	\N	0	\N
1019	forgotten password	content	RTE	8	1	0	\N	<p>\n<a href="/page/9">Forgotten your password since your last visit?</a>  \n</p>			\N		2008-10-12 22:54:29	2008-10-12 22:54:44	1	1	1000		0	N;			N;	N;	0	\N	\N	0		0	0	\N	0	\N
1033	Main Page 1	page	default	1	0	0		\N					2011-01-16 15:53:01	2011-01-16 15:56:13	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	0	0		0	0	\N	0	\N
1034	Main Page 2	page	default	1	0	0		\N					2011-01-16 15:53:08	2011-01-16 15:56:18	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	0	0		0	0	\N	0	\N
1035	Main Page 3	page	default	1	0	0		\N					2011-01-16 15:53:14	2011-01-16 15:56:23	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	0	0		0	0	\N	0	\N
23	About Us	page	default	88	0	30		\N					2006-09-30 12:09:30	2010-04-18 13:01:32	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	\N	0		0	0	\N	0	\N
94	Bin	page	default	0	0	0		\N					2014-12-07 00:00:00	2014-12-07 00:00:00	0	0	1000		0	\N			\N	\N	1	0	0	0		0	0		0	\N
1020	Payment information	content	RTE	8	2	0	\N	<h3>Payment information </h3>\n<p>\nWe accept the following payment methods: \n</p>\n<p>\n<img src="https://www.worldpay.com/cgenerator/logos/visa.gif" alt="Visa payments supported by WorldPay" />\n<img src="https://www.worldpay.com/cgenerator/logos/visa_delta.gif" alt="Visa/Delta payments supported by WorldPay" />\n<img src="https://www.worldpay.com/cgenerator/logos/mastercard.gif" alt="Mastercard payments supported by WorldPay" />\n<img src="https://www.worldpay.com/cgenerator/logos/switch.gif" alt="Switch payments supported by WorldPay" />\n</p>\n<h3>Terms and conditions</h3>\n<p>\nBy making a purchase from this site you are agreeing to be bound by our <a href="/page/26">terms and conditions</a> .\n</p>\n<h3>Payment services </h3>\n<p>\n<!-- Powered by WorldPay logo-->\n<a href="http://www.worldpay.com/"><img src="https://www.worldpay.com/cgenerator/logos/poweredByWorldPay.gif" alt="Powered By WorldPay" /></a>\n</p>\n<p>\n<!-- WorldPay Guarantee Logo -->\n<img src="https://www.worldpay.com/cgenerator/logos/guaranteed.gif" alt="WorldPay Guarantee" />\n</p>			\N		2008-10-12 23:04:24	2008-10-12 23:11:21	1	1	1000		0	N;			N;	N;	0	\N	\N	0		0	0	\N	0	\N
1022	Related products	content	component	6	2	0	\N	\N			\N		2008-10-12 23:17:09	2008-10-12 23:18:43	1	1	1000		0	N;			a:3:{s:8:"template";s:37:"ecommerce/product_related_basket.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
1021	Recently viewed	content	component	6	1	0	\N	\N			\N		2008-10-12 23:16:14	2008-10-12 23:18:55	1	1	1000		0	N;			a:3:{s:8:"template";s:39:"ecommerce/recently_viewed_products.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
42	Address component	content	component	7	2	0	\N				\N		2006-09-30 14:54:43	2008-08-24 18:24:18	1	1	1000		0	N;			a:3:{s:8:"template";s:19:"client/address.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
1024	Userbox	content	component	15	2	0	\N	\N					2010-04-18 12:43:47	2010-04-18 12:44:21	1	1	1000		0	N;			a:3:{s:8:"template";s:19:"client/userbox.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
1027	Text size	content	RTE	1026	1	0	\N	<p>If you would prefer the font size to be larger or smaller, you can easily adjust it using your browser.</p>\n<p>To increase or decrease the text size, follow these basic instructions   for Internet Explorer:</p>\n<ul>\n<li>Click on the 'View' menu in your browser.</li>\n<li>Click on the 'Text size' option.</li>\n<li>Select the size you wish to see.</li>\n</ul>\n<p>To increase or decrease the text size, follow these basic instructions for Mozilla Firefox:</p>\n<ul>\n<li>Click on the 'View' menu in your browser.</li>\n<li>Click on the 'Zoom' option.</li>\n</ul>					2010-04-18 13:02:48	2010-04-18 13:03:20	1	1	1000		0	N;			N;	N;	1	\N	\N	0		0	0	\N	0	\N
1023	content 1242392830	content	RTE	5	1	0	\N	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\n<ul>\n<li>velit esse cillum dolore</li>\n<li>consectetur adipisicing elit</li>\n<li>occaecat cupidatat non proident</li>\n</ul>\n<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>					2009-05-15 14:07:10	2009-05-15 14:07:16	1	1	1000		0	N;			N;	N;	0	\N	\N	0		0	0	\N	0	\N
45	Address Management Component	content	component	16	1	0	\N				\N		2006-09-30 15:20:05	2008-08-24 18:25:00	1	1	1000		0	N;			a:3:{s:8:"template";s:24:"client/address_edit.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
87	General content 2	content	RTE	85	0	0	\N	<p style="text-align: center;"><em>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</em></p>			\N		2006-09-30 15:50:10	2011-01-16 16:11:40	1	1	1000		0	N;			N;	N;	0	\N	0	0		0	0	\N	0	\N
1011	Our Address	content	RTE	20	2	5	\N	<p>Address Name<br />Street, house number xxx<br />Post Code, Town<br />County</p>\n<p>tel: xxxx xxx xxx</p>					2008-08-07 01:18:33	2011-01-16 16:07:10	1	1	1000		0	N;			N;	N;	1	\N	\N	0		0	0	\N	0	\N
57	Password reset component	content	component	9	1	0	\N				\N		2006-09-30 15:30:31	2008-08-24 18:26:03	1	1	1000		0	N;			a:3:{s:8:"template";s:26:"client/password_reset.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
48	Your orders with us	content	component	17	1	0	\N				\N		2006-09-30 15:21:35	2008-08-16 13:22:33	1	1	1000		0	N;			a:3:{s:8:"template";s:25:"ecommerce/order_list.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	1	\N	\N	0		0	0	\N	0	\N
63	Payment failure component	content	component	11	1	0	\N				\N		2006-09-30 15:42:05	2008-08-24 18:26:38	1	1	1000		0	N;			a:3:{s:8:"template";s:37:"ecommerce/payment/protx_callback.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
66	Payment success component	content	component	12	1	0	\N				\N		2006-09-30 15:44:42	2008-08-16 13:28:47	1	1	1000		0	N;			a:3:{s:8:"template";s:37:"ecommerce/payment/protx_callback.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	1	\N	\N	0		0	0	\N	0	\N
2	Commerce	container	default	0	0	0	\N	\N			\N		2006-09-30 09:55:17	2008-08-24 22:58:05	1	0	1000		0	N;			N;	N;	1	\N	\N	0		0	0	\N	0	\N
88	Global navigation	container	default	0	0	15		\N					2009-08-16 13:05:12	2011-01-16 15:56:37	1	1	1000		0	N;			N;	N;	1	0	0	0		0	0	\N	0	\N
21	Search	page	default	4	0	0		\N					2006-09-30 12:08:07	2009-05-15 13:48:10	1	0	1000		0	N;		fibonacci-2-1	N;	N;	1	0	\N	0		0	0	\N	0	\N
1032	content 1295192947	content	RTE	1031	1	0	\N	<h3>Our privacy policy</h3>\n<p>This privacy policy sets out how [COMPANY NAME] uses and protects any information that you give [COMPANY NAME] when you use this website.</p>\n<p>[COMPANY NAME] is committed to ensuring that your privacy is protected. Should we ask you to provide certain information by which you can be identified when using this website, then you can be assured that it will only be used in accordance with this privacy statement.</p>\n<p>[COMPANY NAME] may change this policy from time to time by updating this page. You should check this page from time to time to ensure that you are happy with any changes. This policy is effective from [DATE].</p>\n<h3>What we collect</h3>\n<p>We may collect the following information:</p>\n<ul>\n<li>name and job title</li>\n<li>contact information including email address</li>\n<li>demographic information such as postcode, preferences and interests</li>\n<li>other information relevant to customer surveys and/or offers</li>\n</ul>\n<h3>What we do with the information we gather</h3>\n<p>We require this information to understand your needs and provide you with a better service, and in particular for the following reasons:</p>\n<ul>\n<li>Internal record keeping.</li>\n<li>We may use the information to improve our products and services.</li>\n<li>We may periodically send promotional emails about new products, special offers or other information which we think you may find interesting using the email address which you have provided.</li>\n<li>From time to time, we may also use your information to contact you for market research purposes. We may contact you by email, phone, fax or mail. We may use the information to customise the website according to your interests.</li>\n</ul>\n<h3>Security</h3>\n<p>We are committed to ensuring that your information is secure. In order to prevent unauthorised access or disclosure we have put in place suitable physical, electronic and managerial procedures to safeguard and secure the information we collect online.</p>\n<h3>How we use cookies</h3>\n<p>A cookie is a small file which asks permission to be placed on your computer's hard drive. Once you agree, the file is added and the cookie helps analyse web traffic or lets you know when you visit a particular site. Cookies allow web applications to respond to you as an individual. The web application can tailor its operations to your needs, likes and dislikes by gathering and remembering information about your preferences.</p>\n<p>We use traffic log cookies to identify which pages are being used. This helps us analyse data about webpage traffic and improve our website in order to tailor it to customer needs. We only use this information for statistical analysis purposes and then the data is removed from the system.</p>\n<p>Overall, cookies help us provide you with a better website, by enabling us to monitor which pages you find useful and which you do not. A cookie in no way gives us access to your computer or any information about you, other than the data you choose to share with us.</p>\n<p>You can choose to accept or decline cookies. Most web browsers automatically accept cookies, but you can usually modify your browser setting to decline cookies if you prefer. This may prevent you from taking full advantage of the website.</p>\n<h3>Links to other websites</h3>\n<p>Our website may contain links to other websites of interest. However, once you have used these links to leave our site, you should note that we do not have any control over that other website. Therefore, we cannot be responsible for the protection and privacy of any information which you provide whilst visiting such sites and such sites are not governed by this privacy statement. You should exercise caution and look at the privacy statement applicable to the website in question.</p>\n<h3>Controlling your personal information</h3>\n<p>You may choose to restrict the collection or use of your personal information in the following ways:</p>\n<ul>\n<li>whenever you are asked to fill in a form on the website, look for the box that you can click to indicate that you do not want the information to be used by anybody for direct marketing purposes</li>\n<li>if you have previously agreed to us using your personal information for direct marketing purposes, you may change your mind at any time by writing to or emailing us at <a href="/page/20">contact page</a></li>\n</ul>\n<p>We will not sell, distribute or lease your personal information to third parties unless we have your permission or are required by law to do so. We may use your personal information to send you promotional information about third parties which we think you may find interesting if you tell us that you wish this to happen.</p>\n<p>You may request details of personal information which we hold about you under the Data Protection Act 1998. A small fee will be payable. If you would like a copy of the information held on you please write to <a href="/page/20">our postal address</a>.</p>\n<p>If you believe that any information we are holding on you is incorrect or incomplete, please write to or email us as soon as possible, at the above address. We will promptly correct any information found to be incorrect.</p>			\N		2011-01-16 15:49:07	2011-01-16 15:51:19	1	1	1000		0	N;			N;	N;	0	\N	0	0		0	0	\N	0	\N
76	Send a message	content	contact_form	20	1	5	\N						2006-09-30 16:00:21	2011-01-16 16:07:08	1	1	1000		0	N;			a:6:{s:7:"mail_to";s:0:"";s:11:"mail_toname";s:0:"";s:15:"node_controller";s:13:"common_simple";s:14:"sending_failed";s:89:"You must complete all the required fields. Required items are marked with an asterisk (*)";s:4:"text";s:25:"Thanks for your feedback.";s:4:"href";s:0:"";}	N;	1	\N	\N	0		0	0	\N	0	\N
5	Home	page	default	88	0	40		\N			White Label Home		2006-09-30 10:02:51	2011-12-13 14:42:40	1	1	1000		0	N;		fibonacci-2-1	N;	N;	0	0	0	0		0	0		0	\N
54	User pref component	content	component	18	1	0	\N				\N		2006-09-30 15:25:21	2008-08-24 18:25:48	1	1	1000		0	N;			a:3:{s:8:"template";s:16:"client/edit.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N
1025	Terms of Use	content	RTE	26	1	25	\N	<p>Welcome to our website. If you continue to browse and use this website you are agreeing to comply with and be bound by the following terms and conditions of use, which together with our privacy policy govern [COMPANY NAME]&rsquo;s relationship with you in relation to this website.</p>\n<p>The term &lsquo;[COMPANY NAME]&rsquo; or &lsquo;us&rsquo; or &lsquo;we&rsquo; refers to the owner of the website whose registered office is [COMPANY ADDRESS]. Our company registration number is [REGISTRATION NUMBER], registered in [REGISTERED COUNTRY]. The term &lsquo;you&rsquo; refers to the user or viewer of our website.</p>\n<p>The use of this website is subject to the following terms of use:</p>\n<ol>\n<li>The content of the pages of this website is for your general information and use only. It is subject to change without notice.</li>\n<li>Neither we nor any third parties provide any warranty or guarantee as to the accuracy, timeliness, performance, completeness or suitability of the information and materials found or offered on this website for any particular purpose. You acknowledge that such information and materials may contain inaccuracies or errors and we expressly exclude liability for any such inaccuracies or errors to the fullest extent permitted by law.</li>\n<li>Your use of any information or materials on this website is entirely at your own risk, for which we shall not be liable. It shall be your own responsibility to ensure that any products, services or information available through this website meet your specific requirements.</li>\n<li>This website contains material which is owned by or licensed to us. This material includes, but is not limited to, the design, layout, look, appearance and graphics. Reproduction is prohibited other than in accordance with the copyright notice, which forms part of these terms and conditions.</li>\n<li>All trademarks reproduced in this website, which are not the property of, or licensed to the operator, are acknowledged on the website.</li>\n<li>Unauthorised use of this website may give rise to a claim for damages and/or be a criminal offence.</li>\n<li>From time to time this website may also include links to other websites. These links are provided for your convenience to provide further information. They do not signify that we endorse the website(s). We have no responsibility for the content of the linked website(s).</li>\n<li>You may not create a link to this website from another website or document without [COMPANY NAME]&rsquo;s prior written consent.</li>\n<li>Your use of this website and any dispute arising out of such use of the website is subject to the laws of England, Scotland, Wales and Northern Ireland.</li>\n</ol>					2010-04-18 12:51:22	2011-01-16 15:50:16	1	1	1000		0	N;			N;	N;	1	\N	0	0		0	0	\N	0	\N
1036	Archive	content	component	83	2	0	\N	\N			\N		2011-01-16 15:55:01	2011-01-16 15:55:17	1	1	1000		0	N;			a:3:{s:8:"template";s:17:"news_archive.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	1	\N	0	0		0	0	\N	0	\N
1	Main navigation	container	default	0	0	10	\N	N;			\N		2006-09-29 18:20:29	2008-08-24 22:57:58	1	1	1000		0	N;			N;	N;	1	\N	\N	0		0	0	\N	0	\N
90	Newsletter	page	default	4	0	0	\N	\N	\N	\N	\N	\N	2010-04-18 11:19:18	2010-04-18 11:19:18	1	0	1000	\N	0	\N		fibonacci-2-1	\N	\N	1	\N	\N	0		0	0	\N	0	\N
84	Articles	page	default	3	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 12:07:59	2006-09-30 12:07:59	1	1	1000	\N	0	\N			\N	\N	\N	\N	\N	0		0	0	\N	0	\N
92	Unsubscribe	page	default	90	0	0	\N	\N	\N	\N	\N	\N	2010-04-18 11:21:40	2010-04-18 11:21:40	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	1	\N	\N	0		0	0	\N	0	\N
1026	Accessibility policy	page	default	4	0	0	\N	\N	\N	\N	\N	\N	2010-04-18 13:02:03	2010-04-18 13:02:03	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	1	\N	\N	0		0	0	\N	0	\N
85	Content bits	container	default	3	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 12:07:59	2006-09-30 12:07:59	1	1	1000	\N	0	\N			\N	\N	\N	\N	\N	0		0	0	\N	0	\N
3	Special	container	default	0	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 09:55:36	2006-09-30 09:55:36	1	0	1000	\N	0	\N			\N	\N	\N	\N	\N	0		0	0	\N	0	\N
4	Footer navigation	container	default	0	0	5		N;			\N		2006-09-30 09:56:36	2011-01-16 15:58:20	1	1	1000		0	N;			N;	N;	1	\N	0	0		0	0	\N	0	\N
14	404	page	default	3	0	0		\N					2006-09-30 11:56:37	2008-08-16 13:06:19	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	\N	0		0	0	\N	0	\N
22	Sitemap	page	default	4	0	0		\N					2006-09-30 12:08:21	2008-08-16 13:06:58	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	\N	0		0	0	\N	0	\N
1013	Laboris nisi ut aliquip	page	news	83	0	0	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do\r\neiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad\r\nminim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip\r\nex ea commodo consequat.</p>\r\n<ul>\r\n<li>velit esse cillum dolore</li>\r\n<li>consectetur adipisicing elit</li>\r\n<li>occaecat cupidatat non proident</li>\r\n</ul>\r\n<p>Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat\r\ncupidatat non proident, sunt in culpa qui officia deserunt mollit anim\r\nid est laborum.</p>					2008-08-16 03:59:19	2008-08-16 12:59:16	1	1	1000		0	N;		fibonacci-2-1	a:2:{s:6:"author";s:0:"";s:13:"allow_comment";i:0;}	N;	1	\N	\N	0		0	0	\N	0	\N
1014	Excepteur sint occaecat	page	news	83	0	0	<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do\r\neiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad\r\nminim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip\r\nex ea commodo consequat.</p>\r\n<ul>\r\n<li>velit esse cillum dolore</li>\r\n<li>consectetur adipisicing elit</li>\r\n<li>occaecat cupidatat non proident</li>\r\n</ul>\r\n<p>Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat\r\ncupidatat non proident, sunt in culpa qui officia deserunt mollit anim\r\nid est laborum.</p>					2008-08-16 03:59:48	2008-08-16 12:58:58	1	1	1000		0	N;		fibonacci-2-1	a:2:{s:6:"author";s:0:"";s:13:"allow_comment";i:0;}	N;	1	\N	\N	0		0	0	\N	0	\N
26	Terms & Conditions	page	default	4	0	0		N;					2006-09-30 13:40:50	2010-04-18 12:50:42	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	\N	0		0	0	\N	0	\N
13	Registration	page	default	2	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 10:36:09	2006-09-30 10:36:09	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	\N	\N	\N	0		0	1	\N	0	\N
8	Login	page	default	2	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 10:35:02	2006-09-30 10:35:02	1	1	1000	\N	0	\N	pageLogin	fibonacci-2-1	\N	\N	\N	\N	\N	0		0	1	\N	0	\N
9	Password reset	page	default	2	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 10:35:15	2006-09-30 10:35:15	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	\N	\N	\N	0		0	1	\N	0	\N
6	Basket	page	default	2	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 10:34:35	2006-09-30 10:34:35	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	\N	\N	\N	0		0	0	\N	0	\N
16	Address Management	page	default	15	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 12:03:13	2006-09-30 12:03:13	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	\N	\N	1	0		0	1	\N	0	\N
17	Orders	page	default	15	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 12:03:28	2006-09-30 12:03:28	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	\N	\N	1	0		0	1	\N	0	\N
18	Personal Details	page	default	15	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 12:03:45	2006-09-30 12:03:45	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	\N	\N	1	0		0	1	\N	0	\N
7	Checkout	page	default	2	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 10:34:54	2006-09-30 10:34:54	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	\N	\N	1	0		0	1	\N	0	\N
10	Payment	page	default	2	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 10:35:29	2006-09-30 10:35:29	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	\N	\N	1	0		0	1	\N	0	\N
11	Payment failure	page	default	2	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 10:35:43	2006-09-30 10:35:43	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	\N	\N	1	0		0	1	\N	0	\N
12	Payment success	page	default	2	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 10:35:59	2006-09-30 10:35:59	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	\N	\N	1	0		0	1	\N	0	\N
19	Order detail	page	default	17	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 12:04:12	2006-09-30 12:04:12	1	0	1000	\N	0	\N		fibonacci-2-1	\N	\N	\N	\N	1	0		0	1	\N	0	\N
1031	Privacy Policy	page	default	4	0	0	\N	\N	\N	\N	\N	\N	2011-01-16 15:45:18	2011-01-16 15:45:18	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	1	\N	\N	0		0	0	\N	0	\N
83	News	page	default	88	0	25		\N					2006-09-30 12:07:59	2011-01-16 15:54:18	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	0	0		0	0	\N	0	\N
15	My Account	page	default	88	0	10	\N	\N	\N	\N	\N	\N	2006-09-30 12:02:53	2006-09-30 12:02:53	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	\N	\N	1	0		0	1	\N	0	\N
20	Contact Us	page	default	88	0	20		\N					2006-09-30 12:07:59	2010-04-18 13:01:35	1	1	1000		0	N;		fibonacci-1-1	N;	N;	1	0	\N	0		0	0	\N	0	\N
\.


--
-- Name: common_node_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_node_id_seq', 1036, true);


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

SELECT pg_catalog.setval('common_session_id_seq', 18, true);


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
77	5	/home	generic
78	6	/basket	generic
79	7	/checkout	generic
80	8	/login	generic
81	9	/password-reset	generic
82	10	/payment	generic
83	11	/payment-failure	generic
84	12	/payment-success	generic
85	13	/registration	generic
86	14	/404	generic
87	15	/my-account	generic
88	16	/my-account/address-management	generic
89	17	/my-account/orders	generic
90	18	/my-account/personal-details	generic
91	19	/my-account/orders/order-detail	generic
92	20	/contact-us	generic
93	21	/search	generic
94	22	/sitemap	generic
95	23	/about-us	generic
96	26	/terms-and-conditions	generic
97	83	/news	generic
98	84	/articles	generic
99	90	/newsletter	generic
100	92	/newsletter/unsubscribe	generic
101	1013	/news/2008/08/16/laboris-nisi-ut-aliquip	generic
102	1014	/news/2008/08/16/excepteur-sint-occaecat	generic
103	1026	/accessibility-policy	generic
104	1031	/privacy-policy	generic
105	1033	/main-page-1	generic
106	1034	/main-page-2	generic
107	1035	/main-page-3	generic
76	94	/bin	generic
\.


--
-- Name: common_uri_mapping_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_uri_mapping_id_seq', 107, true);


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

COPY ecommerce_delivery_carrier (id, title, description, limit_list_countries, limit_list_products, limit_list_product_types, limit_order_value, fixed_value, fixed_percentage, priority, publish, free_delivery_map) FROM stdin;
1	Standard	\N	\N	\N	\N	0.00000	5.00000	0.00	0	0	\N
2	Royal Mail 1st Class Post	\N	\N	\N	\N	0.00000	0.00000	0.00	0	1	\N
3	DHL Courier	\N	222	\N	\N	0.00000	7.00000	0.00	0	1	\N
4	UPS	\N	\N	\N	\N	0.00000	0.00000	0.00	0	0	\N
5	Courier	\N	\N	\N	\N	0.00000	0.00000	0.00	0	0	\N
6	Download	\N	\N	\N	\N	0.00000	0.00000	0.00	0	0	\N
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

COPY ecommerce_delivery_carrier_zone (id, name, carrier_id) FROM stdin;
1	UK	2
2	Zone 1	2
3	Zone 2	2
4	Zone 3	2
5	Zone 4	2
6	Zone 5	2
7	Zone 6	2
8	Zone 7	2
9	Zone 8	2
10	Zone 9	2
11	Zone 10	2
12	Zone 11	2
\.


--
-- Name: ecommerce_delivery_carrier_zone_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_delivery_carrier_zone_id_seq', 12, true);


--
-- Data for Name: ecommerce_delivery_carrier_zone_price; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_delivery_carrier_zone_price (id, zone_id, weight, price, currency_code) FROM stdin;
1	1	60	1.30	GBP
2	2	60	2.03	GBP
3	3	60	2.03	GBP
4	4	60	2.03	GBP
5	5	60	2.03	GBP
6	6	60	2.03	GBP
7	7	60	2.03	GBP
8	8	60	2.37	GBP
9	9	60	2.37	GBP
10	10	60	2.37	GBP
11	11	60	2.37	GBP
12	12	60	2.37	GBP
13	1	80	1.46	GBP
14	2	80	2.03	GBP
15	3	80	2.03	GBP
16	4	80	2.03	GBP
17	5	80	2.03	GBP
18	6	80	2.03	GBP
19	7	80	2.03	GBP
20	8	80	2.37	GBP
21	9	80	2.37	GBP
22	10	80	2.37	GBP
23	11	80	2.37	GBP
24	12	80	2.37	GBP
25	1	100	1.46	GBP
26	2	100	2.03	GBP
27	3	100	2.03	GBP
28	4	100	2.03	GBP
29	5	100	2.03	GBP
30	6	100	2.03	GBP
31	7	100	2.03	GBP
32	8	100	2.37	GBP
33	9	100	2.37	GBP
34	10	100	2.37	GBP
35	11	100	2.37	GBP
36	12	100	2.37	GBP
37	1	150	1.64	GBP
38	2	150	2.35	GBP
39	3	150	2.35	GBP
40	4	150	2.35	GBP
41	5	150	2.35	GBP
42	6	150	2.35	GBP
43	7	150	2.35	GBP
44	8	150	2.75	GBP
45	9	150	2.75	GBP
46	10	150	2.75	GBP
47	11	150	2.75	GBP
48	12	150	2.75	GBP
49	1	200	1.79	GBP
50	2	200	2.58	GBP
51	3	200	2.58	GBP
52	4	200	2.58	GBP
53	5	200	2.58	GBP
54	6	200	2.58	GBP
55	7	200	2.58	GBP
56	8	200	3.32	GBP
57	9	200	3.32	GBP
58	10	200	3.32	GBP
59	11	200	3.32	GBP
60	12	200	3.32	GBP
61	1	250	1.94	GBP
62	2	250	2.79	GBP
63	3	250	2.79	GBP
64	4	250	2.79	GBP
65	5	250	2.79	GBP
66	6	250	2.79	GBP
67	7	250	2.79	GBP
68	8	250	3.70	GBP
69	9	250	3.70	GBP
70	10	250	3.70	GBP
71	11	250	3.70	GBP
72	12	250	3.70	GBP
73	1	300	2.07	GBP
74	2	300	3.09	GBP
75	3	300	3.09	GBP
76	4	300	3.09	GBP
77	5	300	3.09	GBP
78	6	300	3.09	GBP
79	7	300	3.09	GBP
80	8	300	4.27	GBP
81	9	300	4.27	GBP
82	10	300	4.27	GBP
83	11	300	4.27	GBP
84	12	300	4.27	GBP
85	1	350	2.21	GBP
86	2	350	3.29	GBP
87	3	350	3.29	GBP
88	4	350	3.29	GBP
89	5	350	3.29	GBP
90	6	350	3.29	GBP
91	7	350	3.29	GBP
92	8	350	4.65	GBP
93	9	350	4.65	GBP
94	10	350	4.65	GBP
95	11	350	4.65	GBP
96	12	350	4.65	GBP
97	1	400	2.40	GBP
98	2	400	3.59	GBP
99	3	400	3.59	GBP
100	4	400	3.59	GBP
101	5	400	3.59	GBP
102	6	400	3.59	GBP
103	7	400	3.59	GBP
104	8	400	5.22	GBP
105	9	400	5.22	GBP
106	10	400	5.22	GBP
107	11	400	5.22	GBP
108	12	400	5.22	GBP
109	1	450	2.59	GBP
110	2	450	3.79	GBP
111	3	450	3.79	GBP
112	4	450	3.79	GBP
113	5	450	3.79	GBP
114	6	450	3.79	GBP
115	7	450	3.79	GBP
116	8	450	5.60	GBP
117	9	450	5.60	GBP
118	10	450	5.60	GBP
119	11	450	5.60	GBP
120	12	450	5.60	GBP
121	1	500	2.78	GBP
122	2	500	4.09	GBP
123	3	500	4.09	GBP
124	4	500	4.09	GBP
125	5	500	4.09	GBP
126	6	500	4.09	GBP
127	7	500	4.09	GBP
128	8	500	6.17	GBP
129	9	500	6.17	GBP
130	10	500	6.17	GBP
131	11	500	6.17	GBP
132	12	500	6.17	GBP
133	1	550	3.15	GBP
134	2	550	4.29	GBP
135	3	550	4.29	GBP
136	4	550	4.29	GBP
137	5	550	4.29	GBP
138	6	550	4.29	GBP
139	7	550	4.29	GBP
140	8	550	6.55	GBP
141	9	550	6.55	GBP
142	10	550	6.55	GBP
143	11	550	6.55	GBP
144	12	550	6.55	GBP
145	1	600	3.15	GBP
146	2	600	4.59	GBP
147	3	600	4.59	GBP
148	4	600	4.59	GBP
149	5	600	4.59	GBP
150	6	600	4.59	GBP
151	7	600	4.59	GBP
152	8	600	7.12	GBP
153	9	600	7.12	GBP
154	10	600	7.12	GBP
155	11	600	7.12	GBP
156	12	600	7.12	GBP
157	1	650	3.52	GBP
158	2	650	4.79	GBP
159	3	650	4.79	GBP
160	4	650	4.79	GBP
161	5	650	4.79	GBP
162	6	650	4.79	GBP
163	7	650	4.79	GBP
164	8	650	7.50	GBP
165	9	650	7.50	GBP
166	10	650	7.50	GBP
167	11	650	7.50	GBP
168	12	650	7.50	GBP
169	1	700	3.52	GBP
170	2	700	5.09	GBP
171	3	700	5.09	GBP
172	4	700	5.09	GBP
173	5	700	5.09	GBP
174	6	700	5.09	GBP
175	7	700	5.09	GBP
176	8	700	8.07	GBP
177	9	700	8.07	GBP
178	10	700	8.07	GBP
179	11	700	8.07	GBP
180	12	700	8.07	GBP
181	1	750	3.71	GBP
182	2	750	5.29	GBP
183	3	750	5.29	GBP
184	4	750	5.29	GBP
185	5	750	5.29	GBP
186	6	750	5.29	GBP
187	7	750	5.29	GBP
188	8	750	8.45	GBP
189	9	750	8.45	GBP
190	10	750	8.45	GBP
191	11	750	8.45	GBP
192	12	750	8.45	GBP
193	1	800	3.90	GBP
194	2	800	5.59	GBP
195	3	800	5.59	GBP
196	4	800	5.59	GBP
197	5	800	5.59	GBP
198	6	800	5.59	GBP
199	7	800	5.59	GBP
200	8	800	9.02	GBP
201	9	800	9.02	GBP
202	10	800	9.02	GBP
203	11	800	9.02	GBP
204	12	800	9.02	GBP
205	1	850	4.27	GBP
206	2	850	5.79	GBP
207	3	850	5.79	GBP
208	4	850	5.79	GBP
209	5	850	5.79	GBP
210	6	850	5.79	GBP
211	7	850	5.79	GBP
212	8	850	9.40	GBP
213	9	850	9.40	GBP
214	10	850	9.40	GBP
215	11	850	9.40	GBP
216	12	850	9.40	GBP
217	1	900	4.27	GBP
218	2	900	6.09	GBP
219	3	900	6.09	GBP
220	4	900	6.09	GBP
221	5	900	6.09	GBP
222	6	900	6.09	GBP
223	7	900	6.09	GBP
224	8	900	9.97	GBP
225	9	900	9.97	GBP
226	10	900	9.97	GBP
227	11	900	9.97	GBP
228	12	900	9.97	GBP
229	1	950	4.64	GBP
230	2	950	6.29	GBP
231	3	950	6.29	GBP
232	4	950	6.29	GBP
233	5	950	6.29	GBP
234	6	950	6.29	GBP
235	7	950	6.29	GBP
236	8	950	10.35	GBP
237	9	950	10.35	GBP
238	10	950	10.35	GBP
239	11	950	10.35	GBP
240	12	950	10.35	GBP
241	1	1000	4.64	GBP
242	2	1000	6.59	GBP
243	3	1000	6.59	GBP
244	4	1000	6.59	GBP
245	5	1000	6.59	GBP
246	6	1000	6.59	GBP
247	7	1000	6.59	GBP
248	8	1000	10.92	GBP
249	9	1000	10.92	GBP
250	10	1000	10.92	GBP
251	11	1000	10.92	GBP
252	12	1000	10.92	GBP
253	1	1100	5.52	GBP
254	2	1100	7.09	GBP
255	3	1100	7.09	GBP
256	4	1100	7.09	GBP
257	5	1100	7.09	GBP
258	6	1100	7.09	GBP
259	7	1100	7.09	GBP
260	8	1100	11.82	GBP
261	9	1100	11.82	GBP
262	10	1100	11.82	GBP
263	11	1100	11.82	GBP
264	12	1100	11.82	GBP
265	1	1200	5.52	GBP
266	2	1200	7.59	GBP
267	3	1200	7.59	GBP
268	4	1200	7.59	GBP
269	5	1200	7.59	GBP
270	6	1200	7.59	GBP
271	7	1200	7.59	GBP
272	8	1200	12.72	GBP
273	9	1200	12.72	GBP
274	10	1200	12.72	GBP
275	11	1200	12.72	GBP
276	12	1200	12.72	GBP
277	1	1300	5.52	GBP
278	2	1300	8.09	GBP
279	3	1300	8.09	GBP
280	4	1300	8.09	GBP
281	5	1300	8.09	GBP
282	6	1300	8.09	GBP
283	7	1300	8.09	GBP
284	8	1300	13.62	GBP
285	9	1300	13.62	GBP
286	10	1300	13.62	GBP
287	11	1300	13.62	GBP
288	12	1300	13.62	GBP
289	1	1400	6.00	GBP
290	2	1400	8.59	GBP
291	3	1400	8.59	GBP
292	4	1400	8.59	GBP
293	5	1400	8.59	GBP
294	6	1400	8.59	GBP
295	7	1400	8.59	GBP
296	8	1400	14.52	GBP
297	9	1400	14.52	GBP
298	10	1400	14.52	GBP
299	11	1400	14.52	GBP
300	12	1400	14.52	GBP
301	1	1500	6.00	GBP
302	2	1500	9.09	GBP
303	3	1500	9.09	GBP
304	4	1500	9.09	GBP
305	5	1500	9.09	GBP
306	6	1500	9.09	GBP
307	7	1500	9.09	GBP
308	8	1500	15.42	GBP
309	9	1500	15.42	GBP
310	10	1500	15.42	GBP
311	11	1500	15.42	GBP
312	12	1500	15.42	GBP
313	1	1600	6.00	GBP
314	2	1600	9.59	GBP
315	3	1600	9.59	GBP
316	4	1600	9.59	GBP
317	5	1600	9.59	GBP
318	6	1600	9.59	GBP
319	7	1600	9.59	GBP
320	8	1600	16.32	GBP
321	9	1600	16.32	GBP
322	10	1600	16.32	GBP
323	11	1600	16.32	GBP
324	12	1600	16.32	GBP
325	1	1700	6.00	GBP
326	2	1700	10.09	GBP
327	3	1700	10.09	GBP
328	4	1700	10.09	GBP
329	5	1700	10.09	GBP
330	6	1700	10.09	GBP
331	7	1700	10.09	GBP
332	8	1700	17.22	GBP
333	9	1700	17.22	GBP
334	10	1700	17.22	GBP
335	11	1700	17.22	GBP
336	12	1700	17.22	GBP
337	1	1800	6.00	GBP
338	2	1800	10.59	GBP
339	3	1800	10.59	GBP
340	4	1800	10.59	GBP
341	5	1800	10.59	GBP
342	6	1800	10.59	GBP
343	7	1800	10.59	GBP
344	8	1800	18.12	GBP
345	9	1800	18.12	GBP
346	10	1800	18.12	GBP
347	11	1800	18.12	GBP
348	12	1800	18.12	GBP
349	1	1900	6.00	GBP
350	2	1900	11.09	GBP
351	3	1900	11.09	GBP
352	4	1900	11.09	GBP
353	5	1900	11.09	GBP
354	6	1900	11.09	GBP
355	7	1900	11.09	GBP
356	8	1900	19.02	GBP
357	9	1900	19.02	GBP
358	10	1900	19.02	GBP
359	11	1900	19.02	GBP
360	12	1900	19.02	GBP
361	1	2000	6.00	GBP
362	2	2000	11.59	GBP
363	3	2000	11.59	GBP
364	4	2000	11.59	GBP
365	5	2000	11.59	GBP
366	6	2000	11.59	GBP
367	7	2000	11.59	GBP
368	8	2000	19.92	GBP
369	9	2000	19.92	GBP
370	10	2000	19.92	GBP
371	11	2000	19.92	GBP
372	12	2000	19.92	GBP
373	1	2500	6.00	GBP
374	2	2500	18.00	GBP
375	3	2500	16.00	GBP
376	4	2500	19.00	GBP
377	5	2500	22.00	GBP
378	6	2500	26.00	GBP
379	7	2500	23.50	GBP
380	8	2500	32.00	GBP
381	9	2500	33.98	GBP
382	10	2500	40.05	GBP
383	11	2500	51.75	GBP
384	12	2500	51.87	GBP
385	1	3000	6.00	GBP
386	2	3000	18.00	GBP
387	3	3000	16.00	GBP
388	4	3000	19.00	GBP
389	5	3000	22.00	GBP
390	6	3000	26.00	GBP
391	7	3000	23.50	GBP
392	8	3000	32.00	GBP
393	9	3000	35.44	GBP
394	10	3000	42.30	GBP
395	11	3000	55.39	GBP
396	12	3000	55.83	GBP
397	1	3500	6.00	GBP
398	2	3500	18.00	GBP
399	3	3500	16.00	GBP
400	4	3500	19.00	GBP
401	5	3500	22.00	GBP
402	6	3500	26.00	GBP
403	7	3500	23.50	GBP
404	8	3500	32.00	GBP
405	9	3500	36.90	GBP
406	10	3500	44.55	GBP
407	11	3500	59.02	GBP
408	12	3500	59.78	GBP
409	1	4000	6.00	GBP
410	2	4000	18.00	GBP
411	3	4000	16.00	GBP
412	4	4000	19.00	GBP
413	5	4000	22.00	GBP
414	6	4000	26.00	GBP
415	7	4000	23.50	GBP
416	8	4000	32.00	GBP
417	9	4000	38.36	GBP
418	10	4000	46.80	GBP
419	11	4000	62.66	GBP
420	12	4000	63.73	GBP
421	1	4500	6.00	GBP
422	2	4500	18.00	GBP
423	3	4500	16.00	GBP
424	4	4500	19.00	GBP
425	5	4500	22.00	GBP
426	6	4500	26.00	GBP
427	7	4500	23.50	GBP
428	8	4500	32.00	GBP
429	9	4500	39.83	GBP
430	10	4500	49.05	GBP
431	11	4500	66.30	GBP
432	12	4500	67.68	GBP
433	1	5000	6.00	GBP
434	2	5000	18.00	GBP
435	3	5000	16.00	GBP
436	4	5000	19.00	GBP
437	5	5000	22.00	GBP
438	6	5000	26.00	GBP
439	7	5000	23.50	GBP
440	8	5000	32.00	GBP
441	9	5000	41.29	GBP
442	10	5000	51.30	GBP
443	11	5000	69.94	GBP
444	12	5000	71.62	GBP
445	1	6000	7.00	GBP
446	2	6000	18.00	GBP
447	3	6000	16.00	GBP
448	4	6000	19.00	GBP
449	5	6000	22.00	GBP
450	6	6000	26.00	GBP
451	7	6000	24.50	GBP
452	8	6000	33.00	GBP
453	9	6000	44.21	GBP
454	10	6000	55.80	GBP
455	11	6000	77.21	GBP
456	12	6000	79.40	GBP
457	1	7000	7.00	GBP
458	2	7000	18.00	GBP
459	3	7000	16.00	GBP
460	4	7000	19.00	GBP
461	5	7000	22.00	GBP
462	6	7000	26.00	GBP
463	7	7000	25.50	GBP
464	8	7000	34.00	GBP
465	9	7000	47.14	GBP
466	10	7000	60.30	GBP
467	11	7000	84.49	GBP
468	12	7000	87.17	GBP
469	1	8000	7.00	GBP
470	2	8000	18.00	GBP
471	3	8000	16.00	GBP
472	4	8000	19.00	GBP
473	5	8000	22.00	GBP
474	6	8000	26.00	GBP
475	7	8000	26.50	GBP
476	8	8000	35.00	GBP
477	9	8000	50.06	GBP
478	10	8000	64.80	GBP
479	11	8000	91.76	GBP
480	12	8000	94.94	GBP
481	1	9000	7.00	GBP
482	2	9000	18.00	GBP
483	3	9000	16.00	GBP
484	4	9000	19.00	GBP
485	5	9000	22.00	GBP
486	6	9000	26.00	GBP
487	7	9000	27.50	GBP
488	8	9000	36.00	GBP
489	9	9000	52.99	GBP
490	10	9000	69.30	GBP
491	11	9000	99.04	GBP
492	12	9000	102.71	GBP
493	1	10000	7.00	GBP
494	2	10000	18.00	GBP
495	3	10000	16.00	GBP
496	4	10000	19.00	GBP
497	5	10000	22.00	GBP
498	6	10000	26.00	GBP
499	7	10000	28.50	GBP
500	8	10000	37.00	GBP
501	9	10000	55.91	GBP
502	10	10000	73.80	GBP
503	11	10000	106.31	GBP
504	12	10000	110.48	GBP
505	1	11000	7.25	GBP
506	2	11000	18.55	GBP
507	3	11000	16.00	GBP
508	4	11000	19.00	GBP
509	5	11000	22.00	GBP
510	6	11000	26.00	GBP
511	7	11000	29.50	GBP
512	8	11000	38.00	GBP
513	9	11000	58.84	GBP
514	10	11000	78.30	GBP
515	11	11000	113.59	GBP
516	12	11000	118.25	GBP
517	1	12000	7.50	GBP
518	2	12000	19.10	GBP
519	3	12000	16.00	GBP
520	4	12000	19.00	GBP
521	5	12000	22.00	GBP
522	6	12000	26.00	GBP
523	7	12000	30.50	GBP
524	8	12000	39.00	GBP
525	9	12000	61.76	GBP
526	10	12000	82.80	GBP
527	11	12000	120.86	GBP
528	12	12000	126.02	GBP
529	1	13000	7.75	GBP
530	2	13000	19.65	GBP
531	3	13000	16.00	GBP
532	4	13000	19.00	GBP
533	5	13000	22.00	GBP
534	6	13000	26.00	GBP
535	7	13000	31.50	GBP
536	8	13000	40.00	GBP
537	9	13000	64.69	GBP
538	10	13000	87.30	GBP
539	11	13000	128.14	GBP
540	12	13000	133.79	GBP
541	1	14000	8.00	GBP
542	2	14000	20.20	GBP
543	3	14000	16.00	GBP
544	4	14000	19.00	GBP
545	5	14000	22.00	GBP
546	6	14000	26.00	GBP
547	7	14000	32.50	GBP
548	8	14000	41.00	GBP
549	9	14000	67.61	GBP
550	10	14000	91.80	GBP
551	11	14000	135.41	GBP
552	12	14000	141.56	GBP
553	1	15000	8.25	GBP
554	2	15000	20.75	GBP
555	3	15000	16.00	GBP
556	4	15000	19.00	GBP
557	5	15000	22.00	GBP
558	6	15000	26.00	GBP
559	7	15000	33.50	GBP
560	8	15000	42.00	GBP
561	9	15000	70.54	GBP
562	10	15000	96.30	GBP
563	11	15000	142.69	GBP
564	12	15000	149.32	GBP
565	1	16000	8.50	GBP
566	2	16000	21.30	GBP
567	3	16000	16.00	GBP
568	4	16000	19.00	GBP
569	5	16000	22.00	GBP
570	6	16000	26.00	GBP
571	7	16000	34.50	GBP
572	8	16000	43.00	GBP
573	9	16000	73.46	GBP
574	10	16000	100.80	GBP
575	11	16000	149.96	GBP
576	12	16000	157.10	GBP
577	1	17000	8.75	GBP
578	2	17000	21.85	GBP
579	3	17000	16.00	GBP
580	4	17000	19.00	GBP
581	5	17000	22.00	GBP
582	6	17000	26.00	GBP
583	7	17000	35.50	GBP
584	8	17000	44.00	GBP
585	9	17000	76.39	GBP
586	10	17000	105.30	GBP
587	11	17000	157.24	GBP
588	12	17000	164.87	GBP
589	1	18000	9.00	GBP
590	2	18000	22.40	GBP
591	3	18000	16.00	GBP
592	4	18000	19.00	GBP
593	5	18000	22.00	GBP
594	6	18000	26.00	GBP
595	7	18000	36.50	GBP
596	8	18000	45.00	GBP
597	9	18000	79.31	GBP
598	10	18000	109.80	GBP
599	11	18000	164.51	GBP
600	12	18000	172.64	GBP
601	1	19000	9.25	GBP
602	2	19000	22.95	GBP
603	3	19000	16.00	GBP
604	4	19000	19.00	GBP
605	5	19000	22.00	GBP
606	6	19000	26.00	GBP
607	7	19000	37.50	GBP
608	8	19000	46.00	GBP
609	9	19000	82.24	GBP
610	10	19000	114.30	GBP
611	11	19000	171.79	GBP
612	12	19000	180.41	GBP
613	1	20000	9.50	GBP
614	2	20000	23.50	GBP
615	3	20000	16.00	GBP
616	4	20000	19.00	GBP
617	5	20000	22.00	GBP
618	6	20000	26.00	GBP
619	7	20000	38.50	GBP
620	8	20000	47.00	GBP
621	9	20000	85.16	GBP
622	10	20000	118.80	GBP
623	11	20000	179.06	GBP
624	12	20000	188.18	GBP
\.


--
-- Name: ecommerce_delivery_carrier_zone_price_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ecommerce_delivery_carrier_zone_price_id_seq', 624, true);


--
-- Data for Name: ecommerce_delivery_carrier_zone_to_country; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ecommerce_delivery_carrier_zone_to_country (id, country_id, zone_id) FROM stdin;
1	1	12
2	2	8
3	3	12
4	4	12
5	5	12
6	6	12
7	7	12
8	8	12
9	9	12
10	10	12
11	11	12
12	12	12
13	13	11
14	14	4
15	15	12
16	16	12
17	17	11
18	18	12
19	19	12
20	20	8
21	21	3
22	22	12
23	23	12
24	24	12
25	25	12
26	26	12
27	27	8
28	28	12
29	29	12
30	30	12
31	31	12
32	32	12
33	33	8
34	34	12
35	35	12
36	36	12
37	37	12
38	38	11
39	39	12
40	40	12
41	41	12
42	42	12
43	43	12
44	44	12
45	45	12
46	46	12
47	47	12
48	48	12
49	49	12
50	50	12
51	51	12
52	52	12
53	53	8
54	54	12
55	55	12
56	56	5
57	57	4
58	58	12
59	59	12
60	60	12
61	61	12
62	62	12
63	63	12
64	64	12
65	65	12
66	66	12
67	67	6
68	68	12
69	69	12
70	70	12
71	71	12
72	72	6
73	73	3
74	75	12
75	76	12
76	77	12
77	78	12
78	79	12
79	80	12
80	81	3
81	82	12
82	83	8
83	84	7
84	85	11
85	86	12
86	87	12
87	88	12
88	89	12
89	90	12
90	91	12
91	92	12
92	93	12
93	94	12
94	95	12
95	96	10
96	97	6
97	98	11
98	99	11
99	100	11
100	101	12
101	102	12
102	103	2
103	104	11
104	105	5
105	106	12
106	107	11
107	108	12
108	109	12
109	110	12
110	111	12
111	112	12
112	113	12
113	114	12
114	115	12
115	116	12
116	117	6
117	118	12
118	119	12
119	120	12
120	121	12
121	122	8
122	123	6
123	124	3
124	125	12
125	126	8
126	127	12
127	74	12
128	128	12
129	129	12
130	130	12
131	131	12
132	132	12
133	133	12
134	134	12
135	135	12
136	136	12
137	137	12
138	138	12
139	139	12
140	140	8
141	141	12
142	142	12
143	143	12
144	144	12
145	145	12
146	146	12
147	147	12
148	148	12
149	149	12
150	150	3
151	151	12
152	152	12
153	153	12
154	154	12
155	155	12
156	156	12
157	157	12
158	158	12
159	159	12
160	160	6
161	161	12
162	162	12
163	163	12
164	164	12
165	165	12
166	166	12
167	167	12
168	168	11
169	169	12
170	170	6
171	171	6
172	172	12
173	173	12
174	174	12
175	175	8
176	176	8
177	177	12
178	178	12
179	179	12
180	180	12
181	181	12
182	182	8
183	183	12
184	184	12
185	185	12
186	186	12
187	187	12
188	188	12
189	189	5
190	190	6
191	191	12
192	192	12
193	193	11
194	194	12
195	195	5
196	196	12
197	197	12
198	198	12
199	199	12
200	200	12
201	201	12
202	202	12
203	203	6
204	204	4
205	205	12
206	206	12
207	207	12
208	208	12
209	209	11
210	210	12
211	211	12
212	212	12
213	213	12
214	214	12
215	215	12
216	216	12
217	217	12
218	218	12
219	219	12
220	220	8
221	221	11
222	222	1
223	223	9
224	224	12
225	225	12
226	226	12
227	227	12
228	228	12
229	229	12
230	230	12
231	231	12
232	232	12
233	233	12
234	234	12
235	235	12
236	236	12
237	237	12
238	238	12
239	239	12
240	240	8
241	241	8
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
1	Hardware	17.5	1
2	Software	17.5	1
3	Energy	5	1
4	Software (only download)	17.5	1
5	Documents  (download)	17.5	1
6	books	0	1
7	Food	17.5	1
8	Food BIO	5	1
9	Generic 1	17.5	1
10	Generic 2	5	1
11	Generic 0	0	1
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
1	Discount Coupon		0	1	2013-11-22 17:36:50	2013-11-22 17:36:50	
2	Referral Invite Coupon		0	1	2013-11-22 17:36:50	2013-11-22 17:36:50	
3	Referral Reward Coupon		0	1	2013-11-22 17:36:50	2013-11-22 17:36:50	
4	Gift Voucher		1	1	2013-11-22 17:36:50	2013-11-22 17:36:50	
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
1	Store		1	2013-11-22 17:36:50	2013-11-22 17:36:50	
2	Supplier		1	2013-11-22 17:36:50	2013-11-22 17:36:50	
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
173	USD	GBP	google	2011-03-24 00:00:00	1.62280000
174	EUR	GBP	google	2011-03-24 00:00:00	1.15075876
175	CZK	GBP	google	2011-03-24 00:00:00	28.07418170
176	AUD	GBP	google	2011-03-24 00:00:00	1.59976341
177	JPY	GBP	google	2011-03-24 00:00:00	131.31574700
178	CAD	GBP	google	2011-03-24 00:00:00	1.58791354
179	HKD	GBP	google	2011-03-24 00:00:00	12.65005770
180	NZD	GBP	google	2011-03-24 00:00:00	2.17504356
\.


--
-- Name: international_currency_rate_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('international_currency_rate_id_seq', 180, true);


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
-- offer_group_id_product_variety_id_key
-- 

ALTER TABLE ONLY ecommerce_offer ADD CONSTRAINT offer_group_id_product_variety_id_key UNIQUE (offer_group_id, product_variety_id);


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
-- Name: ecommerce_delivery_carrier_zone_price_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ecommerce_delivery_carrier_zone_price
    ADD CONSTRAINT ecommerce_delivery_carrier_zone_price_pkey PRIMARY KEY (id);


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
-- Name: ecommerce_delivery_carrier_zone_carrier_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_delivery_carrier_zone
    ADD CONSTRAINT ecommerce_delivery_carrier_zone_carrier_id_fkey FOREIGN KEY (carrier_id) REFERENCES ecommerce_delivery_carrier(id) ON UPDATE CASCADE ON DELETE CASCADE;


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
-- Name: shipping_wz_country_to_zone_country_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_delivery_carrier_zone_to_country
    ADD CONSTRAINT shipping_wz_country_to_zone_country_id_fkey FOREIGN KEY (country_id) REFERENCES international_country(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: shipping_wz_country_to_zone_zone_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_delivery_carrier_zone_to_country
    ADD CONSTRAINT shipping_wz_country_to_zone_zone_id_fkey FOREIGN KEY (zone_id) REFERENCES ecommerce_delivery_carrier_zone(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: shipping_wz_zone_price_zone_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ecommerce_delivery_carrier_zone_price
    ADD CONSTRAINT shipping_wz_zone_price_zone_id_fkey FOREIGN KEY (zone_id) REFERENCES ecommerce_delivery_carrier_zone(id) ON UPDATE CASCADE ON DELETE CASCADE;


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

