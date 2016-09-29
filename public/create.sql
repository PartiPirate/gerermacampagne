SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données :  `gerermacampagne`
--

-- --------------------------------------------------------

--
-- Structure de la table `addresses`
--

CREATE TABLE `addresses` (
  `add_id` int(11) NOT NULL,
  `add_company_name` varchar(255) NOT NULL,
  `add_entity` varchar(255) NOT NULL,
  `add_line_1` varchar(255) NOT NULL,
  `add_line_2` varchar(255) NOT NULL,
  `add_zip_code` varchar(255) NOT NULL,
  `add_city` varchar(255) NOT NULL,
  `add_country_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `book_inlines`
--

CREATE TABLE `book_inlines` (
  `bin_id` int(11) NOT NULL,
  `bin_campaign_id` int(11) NOT NULL,
  `bin_label` varchar(255) NOT NULL,
  `bin_amount` decimal(10,2) NOT NULL,
  `bin_book` enum('campaign','ballot') NOT NULL,
  `bin_column` enum('input','output') NOT NULL COMMENT 'input => credit, output => debit',
  `bin_type` enum('invoice','donation','salary','loaning') NOT NULL,
  `bin_transaction_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `campaigns`
--

CREATE TABLE `campaigns` (
  `cam_id` int(11) NOT NULL,
  `cam_campaign_template_id` int(11) DEFAULT NULL,
  `cam_webdav` varchar(255) NOT NULL,
  `cam_name` varchar(255) NOT NULL,
  `cam_start_date` date NOT NULL DEFAULT '0000-00-00',
  `cam_finish_date` date NOT NULL DEFAULT '0000-00-00',
  `cam_electoral_district` varchar(255) NOT NULL,
  `cam_political_party_id` int(11) NOT NULL,
  `cam_political_party_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `campaign_templates`
--

CREATE TABLE `campaign_templates` (
  `cte_id` int(11) NOT NULL,
  `cte_label` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `candidatures`
--

CREATE TABLE `candidatures` (
  `can_id` int(11) NOT NULL,
  `can_sex` enum('male','female') NOT NULL DEFAULT 'male',
  `can_firstname` varchar(255) NOT NULL,
  `can_lastname` varchar(255) NOT NULL,
  `can_mail` varchar(255) NOT NULL,
  `can_telephone` varchar(255) NOT NULL,
  `can_authorize` tinyint(4) NOT NULL DEFAULT '0',
  `can_address_id` int(11) NOT NULL,
  `can_bodyshot_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `candidature_circonscriptions`
--

CREATE TABLE `candidature_circonscriptions` (
  `cci_id` int(11) NOT NULL,
  `cci_candidature_id` int(11) NOT NULL,
  `cci_circonscription` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `candidature_positions`
--

CREATE TABLE `candidature_positions` (
  `cpo_id` int(11) NOT NULL,
  `cpo_candidature_id` int(11) NOT NULL,
  `cpo_position` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `documents`
--

CREATE TABLE `documents` (
  `doc_id` int(11) NOT NULL,
  `doc_political_party_id` int(11) DEFAULT NULL,
  `doc_campaign_id` int(11) DEFAULT NULL,
  `doc_task_id` int(11) DEFAULT NULL,
  `doc_label` varchar(255) NOT NULL,
  `doc_name` varchar(255) NOT NULL,
  `doc_size` int(11) NOT NULL,
  `doc_mime_type` varchar(255) NOT NULL,
  `doc_path` varchar(255) NOT NULL,
  `doc_creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `doc_modification_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `donations`
--

CREATE TABLE `donations` (
  `don_id` int(11) NOT NULL,
  `don_book_inline_id` int(11) NOT NULL,
  `don_firstname` varchar(255) NOT NULL,
  `don_lastname` varchar(255) NOT NULL,
  `don_address_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `inline_documents`
--

CREATE TABLE `inline_documents` (
  `ido_id` int(11) NOT NULL,
  `ido_document_id` int(11) NOT NULL,
  `ido_book_inline_id` int(11) NOT NULL,
  `ido_type` enum('invoice','quotation','order','check') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `log_actions`
--

CREATE TABLE `log_actions` (
  `lac_id` bigint(20) NOT NULL,
  `lac_label` varchar(255) NOT NULL,
  `lac_status` tinyint(4) NOT NULL,
  `lac_login` varchar(255) NOT NULL,
  `lac_ip` varchar(255) NOT NULL,
  `lac_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `political_parties`
--

CREATE TABLE `political_parties` (
  `ppa_id` int(11) NOT NULL,
  `ppa_address_id` int(11) NOT NULL,
  `ppa_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `tasks`
--

CREATE TABLE `tasks` (
  `tas_id` int(11) NOT NULL,
  `tas_campaign_id` int(11) NOT NULL,
  `tas_order` int(11) NOT NULL,
  `tas_dependencies` varchar(255) NOT NULL,
  `tas_implies` varchar(255) NOT NULL DEFAULT '[]',
  `tas_label` varchar(255) NOT NULL,
  `tas_form` varchar(255) NOT NULL DEFAULT 'doTask',
  `tas_righters` varchar(255) NOT NULL,
  `tas_limit_date` date NOT NULL DEFAULT '0000-00-00',
  `tas_status` enum('inProgress','done') NOT NULL,
  `tas_documents` varchar(10000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `task_models`
--

CREATE TABLE `task_models` (
  `tmo_id` int(11) NOT NULL,
  `tmo_campaign_template_id` int(11) DEFAULT NULL,
  `tmo_dependencies` varchar(255) NOT NULL DEFAULT '[]',
  `tmo_implies` varchar(255) NOT NULL DEFAULT '[]',
  `tmo_label` varchar(255) NOT NULL,
  `tmo_form` varchar(255) NOT NULL DEFAULT 'doTask',
  `tmo_righters` varchar(255) NOT NULL DEFAULT '[]',
  `tmo_documents` varchar(10000) NOT NULL DEFAULT '[]',
  `tmo_order` int(11) NOT NULL,
  `tmo_computation_date` varchar(2047) NOT NULL DEFAULT '{}' COMMENT 'JSON Object for effective computed date of task'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `telephones`
--

CREATE TABLE `telephones` (
  `tel_id` int(11) NOT NULL,
  `tel_user_id` int(11) NOT NULL,
  `tel_telephone` varchar(255) NOT NULL,
  `tel_type` enum('telephone','fax') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `use_id` int(11) NOT NULL,
  `use_login` varchar(255) NOT NULL,
  `use_password` varchar(255) NOT NULL,
  `use_address_id` int(11) NOT NULL,
  `use_activated` tinyint(1) NOT NULL DEFAULT '0',
  `use_activation_key` varchar(255) NOT NULL,
  `use_mail` varchar(255) NOT NULL,
  `use_language` char(2) NOT NULL DEFAULT 'fr'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `user_current_campaigns`
--

CREATE TABLE `user_current_campaigns` (
  `ucc_id` int(11) NOT NULL,
  `ucc_user_id` int(11) NOT NULL,
  `ucc_campaign_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `user_rights`
--

CREATE TABLE `user_rights` (
  `uri_id` int(11) NOT NULL,
  `uri_user_id` int(11) NOT NULL,
  `uri_right` enum('partyAdmin','listHead','candidate','substitute','representative','charteredAccountant') NOT NULL,
  `uri_target_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `voting_papers`
--

CREATE TABLE `voting_papers` (
  `vpa_id` int(11) NOT NULL,
  `vpa_campaign_id` int(11) NOT NULL,
  `vap_status` enum('active','deleted','inconstruction','') NOT NULL DEFAULT 'inconstruction',
  `vpa_format` enum('105x148','148x210','210x297') NOT NULL,
  `vpa_code` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`add_id`);

--
-- Index pour la table `book_inlines`
--
ALTER TABLE `book_inlines`
  ADD PRIMARY KEY (`bin_id`);

--
-- Index pour la table `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`cam_id`),
  ADD KEY `cam_political_party_id` (`cam_political_party_id`),
  ADD KEY `cam_campaign_template_id` (`cam_campaign_template_id`);

--
-- Index pour la table `campaign_templates`
--
ALTER TABLE `campaign_templates`
  ADD PRIMARY KEY (`cte_id`);

--
-- Index pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD PRIMARY KEY (`can_id`);

--
-- Index pour la table `candidature_circonscriptions`
--
ALTER TABLE `candidature_circonscriptions`
  ADD PRIMARY KEY (`cci_id`);

--
-- Index pour la table `candidature_positions`
--
ALTER TABLE `candidature_positions`
  ADD PRIMARY KEY (`cpo_id`);

--
-- Index pour la table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`doc_id`);

--
-- Index pour la table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`don_id`);

--
-- Index pour la table `inline_documents`
--
ALTER TABLE `inline_documents`
  ADD PRIMARY KEY (`ido_id`);

--
-- Index pour la table `log_actions`
--
ALTER TABLE `log_actions`
  ADD PRIMARY KEY (`lac_id`);

--
-- Index pour la table `political_parties`
--
ALTER TABLE `political_parties`
  ADD PRIMARY KEY (`ppa_id`),
  ADD KEY `ppa_address_id` (`ppa_address_id`);

--
-- Index pour la table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`tas_id`);

--
-- Index pour la table `task_models`
--
ALTER TABLE `task_models`
  ADD PRIMARY KEY (`tmo_id`),
  ADD KEY `tmo_campaign_template_id` (`tmo_campaign_template_id`);

--
-- Index pour la table `telephones`
--
ALTER TABLE `telephones`
  ADD PRIMARY KEY (`tel_id`),
  ADD KEY `tel_user_id` (`tel_user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`use_id`),
  ADD KEY `use_login` (`use_login`),
  ADD KEY `use_address_id` (`use_address_id`);

--
-- Index pour la table `user_current_campaigns`
--
ALTER TABLE `user_current_campaigns`
  ADD PRIMARY KEY (`ucc_id`),
  ADD KEY `ucc_user_id` (`ucc_user_id`),
  ADD KEY `ucc_campaign_id` (`ucc_campaign_id`);

--
-- Index pour la table `user_rights`
--
ALTER TABLE `user_rights`
  ADD PRIMARY KEY (`uri_id`),
  ADD UNIQUE KEY `uri_user_id` (`uri_user_id`,`uri_right`,`uri_target_id`);

--
-- Index pour la table `voting_papers`
--
ALTER TABLE `voting_papers`
  ADD PRIMARY KEY (`vpa_id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `add_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `book_inlines`
--
ALTER TABLE `book_inlines`
  MODIFY `bin_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `cam_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `campaign_templates`
--
ALTER TABLE `campaign_templates`
  MODIFY `cte_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `candidatures`
--
ALTER TABLE `candidatures`
  MODIFY `can_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `candidature_circonscriptions`
--
ALTER TABLE `candidature_circonscriptions`
  MODIFY `cci_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `candidature_positions`
--
ALTER TABLE `candidature_positions`
  MODIFY `cpo_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `documents`
--
ALTER TABLE `documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `donations`
--
ALTER TABLE `donations`
  MODIFY `don_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `inline_documents`
--
ALTER TABLE `inline_documents`
  MODIFY `ido_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `log_actions`
--
ALTER TABLE `log_actions`
  MODIFY `lac_id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `political_parties`
--
ALTER TABLE `political_parties`
  MODIFY `ppa_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `tas_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `task_models`
--
ALTER TABLE `task_models`
  MODIFY `tmo_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `telephones`
--
ALTER TABLE `telephones`
  MODIFY `tel_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `use_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `user_current_campaigns`
--
ALTER TABLE `user_current_campaigns`
  MODIFY `ucc_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `user_rights`
--
ALTER TABLE `user_rights`
  MODIFY `uri_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `voting_papers`
--
ALTER TABLE `voting_papers`
  MODIFY `vpa_id` int(11) NOT NULL AUTO_INCREMENT;