--
-- PostgreSQL database dump
--

\restrict jF3YlOX1T43fbqGwcCWP9XWAFoCgydyvFjR2eJhU7GvBPIZy90OL9eWKfR0AsO0

-- Dumped from database version 17.11 (Debian 17.11-1.pgdg13+2)
-- Dumped by pg_dump version 17.11 (Debian 17.11-1.pgdg13+2)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: alternativas_inversion; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.alternativas_inversion (
    id bigint NOT NULL,
    nombre character varying(150) NOT NULL,
    descripcion text NOT NULL,
    inversion_inicial numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    costo_operativo_anual numeric(12,2) NOT NULL,
    ingreso_anual numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    vida_util_anios smallint DEFAULT '5'::smallint NOT NULL,
    valor_salvamento numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    tasa_tmar numeric(5,2) NOT NULL,
    justificacion_seleccion text,
    detalle_inversion text,
    detalle_costos_operativos text,
    fuentes_verificacion text,
    empresa_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: alternativas_inversion_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.alternativas_inversion_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: alternativas_inversion_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.alternativas_inversion_id_seq OWNED BY public.alternativas_inversion.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: costo_conceptos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.costo_conceptos (
    id bigint NOT NULL,
    concepto character varying(150) NOT NULL,
    monto_anual numeric(12,2) NOT NULL,
    fuente_evidencia_id bigint,
    fuente_nota character varying(255),
    alternativa_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: costo_conceptos_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.costo_conceptos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: costo_conceptos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.costo_conceptos_id_seq OWNED BY public.costo_conceptos.id;


--
-- Name: empresas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.empresas (
    id bigint NOT NULL,
    nombre character varying(150) DEFAULT '4Seasons'::character varying NOT NULL,
    giro character varying(255) DEFAULT 'Comercialización de prendas de vestir y accesorios'::character varying NOT NULL,
    propietario_contacto character varying(150) NOT NULL,
    telefono character varying(20) NOT NULL,
    tasa_impuesto numeric(5,2) DEFAULT '30'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: empresas_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.empresas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: empresas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.empresas_id_seq OWNED BY public.empresas.id;


--
-- Name: evidencias_documentales; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.evidencias_documentales (
    id bigint NOT NULL,
    tipo character varying(20) NOT NULL,
    descripcion character varying(255) NOT NULL,
    archivo character varying(255) NOT NULL,
    fecha_registro date DEFAULT CURRENT_DATE NOT NULL,
    alternativa_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: evidencias_documentales_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.evidencias_documentales_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: evidencias_documentales_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.evidencias_documentales_id_seq OWNED BY public.evidencias_documentales.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: fuente_datos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.fuente_datos (
    id bigint NOT NULL,
    dato character varying(20) NOT NULL,
    fuente_nota character varying(255),
    alternativa_id bigint NOT NULL,
    fuente_evidencia_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: fuente_datos_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.fuente_datos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: fuente_datos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.fuente_datos_id_seq OWNED BY public.fuente_datos.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: alternativas_inversion id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.alternativas_inversion ALTER COLUMN id SET DEFAULT nextval('public.alternativas_inversion_id_seq'::regclass);


--
-- Name: costo_conceptos id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.costo_conceptos ALTER COLUMN id SET DEFAULT nextval('public.costo_conceptos_id_seq'::regclass);


--
-- Name: empresas id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.empresas ALTER COLUMN id SET DEFAULT nextval('public.empresas_id_seq'::regclass);


--
-- Name: evidencias_documentales id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.evidencias_documentales ALTER COLUMN id SET DEFAULT nextval('public.evidencias_documentales_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: fuente_datos id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.fuente_datos ALTER COLUMN id SET DEFAULT nextval('public.fuente_datos_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: alternativas_inversion; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.alternativas_inversion (id, nombre, descripcion, inversion_inicial, costo_operativo_anual, ingreso_anual, vida_util_anios, valor_salvamento, tasa_tmar, justificacion_seleccion, detalle_inversion, detalle_costos_operativos, fuentes_verificacion, empresa_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: costo_conceptos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.costo_conceptos (id, concepto, monto_anual, fuente_evidencia_id, fuente_nota, alternativa_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: empresas; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.empresas (id, nombre, giro, propietario_contacto, telefono, tasa_impuesto, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: evidencias_documentales; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.evidencias_documentales (id, tipo, descripcion, archivo, fecha_registro, alternativa_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: fuente_datos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.fuente_datos (id, dato, fuente_nota, alternativa_id, fuente_evidencia_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_11_23_043721_create_empresas_table	1
5	2026_11_23_043803_create_alternativas_inversion_table	1
6	2026_11_23_043813_create_evidencias_documentales_table	1
7	2026_11_23_043823_create_costo_conceptos_table	1
8	2026_11_23_043832_create_fuente_datos_table	1
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at) FROM stdin;
\.


--
-- Name: alternativas_inversion_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.alternativas_inversion_id_seq', 1, false);


--
-- Name: costo_conceptos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.costo_conceptos_id_seq', 1, false);


--
-- Name: empresas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.empresas_id_seq', 1, false);


--
-- Name: evidencias_documentales_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.evidencias_documentales_id_seq', 1, false);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: fuente_datos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.fuente_datos_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 8, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_id_seq', 1, false);


--
-- Name: alternativas_inversion alternativas_inversion_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.alternativas_inversion
    ADD CONSTRAINT alternativas_inversion_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: costo_conceptos costo_conceptos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.costo_conceptos
    ADD CONSTRAINT costo_conceptos_pkey PRIMARY KEY (id);


--
-- Name: empresas empresas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.empresas
    ADD CONSTRAINT empresas_pkey PRIMARY KEY (id);


--
-- Name: evidencias_documentales evidencias_documentales_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.evidencias_documentales
    ADD CONSTRAINT evidencias_documentales_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: fuente_datos fuente_datos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.fuente_datos
    ADD CONSTRAINT fuente_datos_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: fuente_datos_alternativa_id_dato_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX fuente_datos_alternativa_id_dato_index ON public.fuente_datos USING btree (alternativa_id, dato);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: alternativas_inversion alternativas_inversion_empresa_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.alternativas_inversion
    ADD CONSTRAINT alternativas_inversion_empresa_id_foreign FOREIGN KEY (empresa_id) REFERENCES public.empresas(id) ON DELETE CASCADE;


--
-- Name: costo_conceptos costo_conceptos_alternativa_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.costo_conceptos
    ADD CONSTRAINT costo_conceptos_alternativa_id_foreign FOREIGN KEY (alternativa_id) REFERENCES public.alternativas_inversion(id) ON DELETE CASCADE;


--
-- Name: costo_conceptos costo_conceptos_fuente_evidencia_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.costo_conceptos
    ADD CONSTRAINT costo_conceptos_fuente_evidencia_id_foreign FOREIGN KEY (fuente_evidencia_id) REFERENCES public.evidencias_documentales(id) ON DELETE SET NULL;


--
-- Name: evidencias_documentales evidencias_documentales_alternativa_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.evidencias_documentales
    ADD CONSTRAINT evidencias_documentales_alternativa_id_foreign FOREIGN KEY (alternativa_id) REFERENCES public.alternativas_inversion(id) ON DELETE CASCADE;


--
-- Name: fuente_datos fuente_datos_alternativa_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.fuente_datos
    ADD CONSTRAINT fuente_datos_alternativa_id_foreign FOREIGN KEY (alternativa_id) REFERENCES public.alternativas_inversion(id) ON DELETE CASCADE;


--
-- Name: fuente_datos fuente_datos_fuente_evidencia_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.fuente_datos
    ADD CONSTRAINT fuente_datos_fuente_evidencia_id_foreign FOREIGN KEY (fuente_evidencia_id) REFERENCES public.evidencias_documentales(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict jF3YlOX1T43fbqGwcCWP9XWAFoCgydyvFjR2eJhU7GvBPIZy90OL9eWKfR0AsO0

