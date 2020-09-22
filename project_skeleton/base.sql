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
    'permissions',
    'build'
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
1	1	222	Mr Onyx Tester	58 Howard Street			BT1 6PJ	Belfast	\N	\N	\N	\N
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
1	Mr	Onyx	Tester	\N	test@onxshop.com	\N	+44(0) 2890 328 988	\N	\N	b3f61bf1cb26243ef478a3c181dd0aa2	0	1	1	\N	2011-12-13 14:00:00	GBP	1	0	\N		2011-12-13 14:00:00	0	0	0	\N	\N	\N	\N	\N	\N	\N	\N
0		Anonym	Anonymous		anonym@noemail.noemail	anonymous	notelephone			9ce21d8f3992d89a325aa9dcf520a591	0	1	1	 	2011-12-13 14:00:00	GBP	0	0	2007-06-14		2011-12-13 14:00:00	0	0	0	\N	\N	\N	\N	\N	\N	\N	\N
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
31	3	build	_all_	\N	2020-09-18 16:00:00.000000	2020-09-18 16:00:00.000000	\N
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
2	0	global	author_content	White Label, http://www.example.com/		0
5	0	global	locale	en_GB.UTF-8		0
6	0	global	default_currency	GBP		0
9	0	global	google_analytics			0
4	0	global	html_title_suffix	- White Label		0
10	0	global	google_adwords			0
13	0	global	extra_body_top			0
14	0	global	extra_body_bottom			0
15	0	global	display_secondary_navigation	0		0
16	0	global	display_content_foot	0		0
17	5	global	html_title_suffix			0
7	0	global	admin_email	test@onxshop.com		0
12	0	global	extra_head			0
11	0	global	display_content_side	0		0
3	0	global	credit			0
8	0	global	css	@import url(/css/v1.css?1);		0
1	0	global	title	basetestdevlaposacouk		0
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
0	Root	site	default	\N	0	0							2008-08-06 21:24:09	2008-08-06 21:24:09	1	1	0		0				\N	\N	\N	\N	\N	0		0	0	\N	0	\N	\N
78	404 error	content	rte	14	0	0	\N	<p><strong>We have recently restructured this website, you might find what you are looking for by going via the <a href="/">home page</a>.</strong></p>\r\n<p><strong>If you believe you have found a broken link please <a href="/page/20">let us know</a>.</strong></p>\r\n<div class="line">\r\n<hr />\r\n</div>\r\n<p><strong>Please try the following:</strong></p>\r\n<ul>\r\n<li>If you typed the page address in the Address bar, make sure that it is spelled correctly. </li>\r\n<li>Click the <a href="javascript:history.go(-1)">Back</a> button to try another link. </li>\r\n</ul>\r\n<p>HTTP 404 : Page not found</p>			\N		2006-09-30 16:37:05	2008-08-24 18:28:28	1	1	1000		0	N;			N;	N;	1	\N	\N	0		0	0	\N	0	\N	\N
94	Bin	page	default	0	0	0		\N					2014-12-07 00:00:00	2014-12-07 00:00:00	0	0	1000		0	\N			\N	\N	1	0	0	0		0	0		0	\N	\N
57	Password reset component	content	component	9	0	0	\N				\N		2006-09-30 15:30:31	2008-08-24 18:26:03	1	1	1000		0	N;			a:3:{s:8:"template";s:26:"client/password_reset.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}	N;	0	\N	\N	0		0	0	\N	0	\N	\N
9	Password reset	page	default	3	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 10:35:15	2006-09-30 10:35:15	1	1	1000	\N	0	\N		fibonacci-2-1	\N	\N	\N	\N	\N	0		0	1	\N	0	\N	\N
1	Primary navigation	container	default	0	0	10	\N	N;			\N		2006-09-29 18:20:29	2008-08-24 22:57:58	1	1	1000		0	N;			N;	N;	1	\N	\N	0		0	0	\N	0	\N	\N
3	Special	container	default	0	0	0	\N	\N	\N	\N	\N	\N	2006-09-30 09:55:36	2006-09-30 09:55:36	1	0	1000	\N	0	\N			\N	\N	\N	\N	\N	0		0	0	\N	0	\N	\N
14	404	page	default	3	0	0		\N					2006-09-30 11:56:37	2008-08-16 13:06:19	1	1	1000		0	N;		fibonacci-2-1	N;	N;	1	0	\N	0		0	0	\N	0	\N	\N
5	Home	page	default	1	0	0		\N		\N			2017-06-27 17:23:27	2017-06-27 17:26:17	1	1	0		0	\N		fibonacci-2-1	a:1:{s:13:"allow_comment";i:0;}	\N	0	0	0	0		0	0		0	0	\N
\.


--
-- Name: common_node_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_node_id_seq', 1000, true);


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
-- Data for Name: common_revision; Type: TABLE DATA; Schema: public; Owner: -
--

COPY common_revision (id, object, node_id, content, status, customer_id, created, modified, other_data) FROM stdin;
1	common_node	1001	a:21:{s:5:"title";s:4:"Home";s:15:"node_controller";s:7:"default";s:10:"node_group";s:4:"page";s:6:"parent";s:1:"1";s:16:"parent_container";s:1:"0";s:13:"display_title";i:1;s:7:"created";s:25:"2017-06-27T17:23:27+01:00";s:8:"modified";s:25:"2017-06-27T17:23:27+01:00";s:7:"publish";i:1;s:8:"priority";i:0;s:11:"customer_id";i:0;s:6:"author";i:0;s:15:"display_in_menu";i:1;s:18:"display_permission";i:0;s:9:"css_class";s:0:"";s:18:"display_breadcrumb";i:0;s:13:"browser_title";s:0:"";s:15:"link_to_node_id";i:0;s:11:"require_ssl";i:0;s:12:"layout_style";s:13:"fibonacci-2-1";s:2:"id";i:1001;}	\N	0	2017-06-27 16:23:27.927397	2017-06-27 16:23:27.927397	\N
2	common_node	5	a:22:{s:5:"title";s:4:"Home";s:10:"page_title";s:0:"";s:9:"uri_title";s:0:"";s:13:"browser_title";s:0:"";s:9:"strapline";s:0:"";s:11:"description";s:0:"";s:9:"css_class";s:0:"";s:15:"display_in_menu";s:1:"1";s:18:"display_permission";s:1:"0";s:15:"node_controller";s:7:"default";s:10:"node_group";s:4:"page";s:11:"customer_id";s:1:"0";s:4:"head";s:0:"";s:7:"publish";i:1;s:2:"id";s:1:"5";s:28:"display_secondary_navigation";i:0;s:9:"component";s:31:"a:1:{s:13:"allow_comment";i:0;}";s:13:"display_title";i:0;s:13:"require_login";i:0;s:11:"require_ssl";i:0;s:8:"modified";s:25:"2017-06-27T17:26:17+01:00";s:28:"display_permission_group_acl";s:0:"";}	\N	0	2017-06-27 16:26:17.542757	2017-06-27 16:26:17.542757	\N
\.


--
-- Name: common_revision_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_revision_id_seq', 2, true);


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
81	9	/password-reset	generic
86	14	/404	generic
76	94	/bin	generic
108	5	/home	generic
\.


--
-- Name: common_uri_mapping_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('common_uri_mapping_id_seq', 108, true);


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

CREATE INDEX education_survey_entry_survey_id_fkey ON education_survey_entry USING btree(survey_id);
CREATE INDEX education_survey_entry_answer_survey_entry_id_fkey ON education_survey_entry_answer USING btree(survey_entry_id);
