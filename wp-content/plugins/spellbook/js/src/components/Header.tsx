import React from 'react';
import './Header.css';
import SpellbookIcon from '../svgs/spellbook-icon.svg';
import SpellbookText from '../svgs/spellbook-text.svg';

const Header: React.FC = () => {
  return (
    <div className="spellbook-app__header gform-modular-sidebar__header">
      <div className="spellbook-app__header-logo">
        <SpellbookIcon width={46} />
      </div>
      <div className="spellbook-app__header-main">
        <SpellbookText width={147} />
      </div>
    </div>
  );
};

export default Header;
