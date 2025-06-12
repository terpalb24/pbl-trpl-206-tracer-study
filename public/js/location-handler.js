/**
 * Location Handler for Multinational Location Selection
 */
class LocationHandler {
    constructor() {
        this.countryData = null;
        this.stateData = {};
        this.loadedCountries = [];
        this.baseUrl = '/js/location-data/';
    }
    
    /**
     * Initialize location handler
     * @param {string} countrySelector - Country select element selector
     * @param {string} stateSelector - State select element selector
     * @param {string} citySelector - City select element selector
     * @param {string} hiddenInputSelector - Hidden input to store combined value
     */
    async init(countrySelector, stateSelector, citySelector, hiddenInputSelector) {
        this.countrySelect = document.querySelector(countrySelector);
        this.stateSelect = document.querySelector(stateSelector);
        this.citySelect = document.querySelector(citySelector);
        this.hiddenInput = document.querySelector(hiddenInputSelector);
        
        if (!this.countrySelect || !this.stateSelect || !this.citySelect || !this.hiddenInput) {
            console.error('One or more selectors not found in DOM');
            return false;
        }
        
        // Fetch countries list
        await this.fetchCountries();
        
        // Setup event listeners
        this.countrySelect.addEventListener('change', () => this.onCountryChange());
        this.stateSelect.addEventListener('change', () => this.onStateChange());
        this.citySelect.addEventListener('change', () => this.updateHiddenInput());
        
        return true;
    }
    
    /**
     * Fetch countries data
     */
    async fetchCountries() {
        try {
            const response = await fetch(`${this.baseUrl}countries.json`);
            const data = await response.json();
            
            if (!data || !data.countries) {
                throw new Error('Invalid countries data format');
            }
            
            this.countryData = data;
            this.populateCountries();
            
        } catch (error) {
            console.error('Failed to fetch countries:', error);
            this.showError(this.countrySelect, 'Failed to load countries data');
        }
    }
    
    /**
     * Populate country dropdown
     */
    populateCountries() {
        if (!this.countryData || !this.countryData.countries) return;
        
        // Clear current options
        this.countrySelect.innerHTML = '<option value="">-- Pilih Negara --</option>';
        
        // Add countries
        this.countryData.countries.forEach(country => {
            const option = document.createElement('option');
            option.value = country.code;
            option.textContent = country.name;
            this.countrySelect.appendChild(option);
        });
    }
    
    /**
     * Handle country selection change
     */
    async onCountryChange() {
        const countryCode = this.countrySelect.value;
        
        // Reset state and city dropdowns
        this.resetStateSelect();
        this.resetCitySelect();
        this.updateHiddenInput();
        
        if (!countryCode) return;
        
        // Check if we've already loaded this country's data
        if (!this.loadedCountries.includes(countryCode)) {
            try {
                await this.fetchStatesForCountry(countryCode);
                this.loadedCountries.push(countryCode);
            } catch (error) {
                console.error(`Failed to fetch states for country ${countryCode}:`, error);
                this.showError(this.stateSelect, 'Failed to load states data');
                return;
            }
        }
        
        this.populateStates(countryCode);
    }
    
    /**
     * Fetch states data for a country
     * @param {string} countryCode - Country code (e.g., 'ID', 'US')
     */
    async fetchStatesForCountry(countryCode) {
        try {
            const response = await fetch(`${this.baseUrl}${countryCode}.json`);
            const data = await response.json();
            
            if (!data || !data.states) {
                throw new Error(`Invalid state data format for country ${countryCode}`);
            }
            
            this.stateData[countryCode] = data;
            
        } catch (error) {
            console.error(`Failed to fetch states for country ${countryCode}:`, error);
            throw error;
        }
    }
    
    /**
     * Populate state dropdown for selected country
     * @param {string} countryCode - Selected country code
     */
    populateStates(countryCode) {
        const countryData = this.stateData[countryCode];
        if (!countryData || !countryData.states) return;
        
        // Clear current options
        this.resetStateSelect();
        
        // Add states
        countryData.states.forEach(state => {
            const option = document.createElement('option');
            option.value = state.code;
            option.textContent = state.name;
            option.dataset.index = countryData.states.indexOf(state);
            this.stateSelect.appendChild(option);
        });
        
        // Enable state select
        this.stateSelect.disabled = false;
    }
    
    /**
     * Handle state selection change
     */
    onStateChange() {
        const countryCode = this.countrySelect.value;
        const stateCode = this.stateSelect.value;
        const stateIndex = this.stateSelect.selectedOptions[0]?.dataset?.index;
        
        // Reset city dropdown
        this.resetCitySelect();
        this.updateHiddenInput();
        
        if (!countryCode || !stateCode || stateIndex === undefined) return;
        
        this.populateCities(countryCode, parseInt(stateIndex));
    }
    
    /**
     * Populate city dropdown for selected state
     * @param {string} countryCode - Selected country code
     * @param {number} stateIndex - Index of selected state in states array
     */
    populateCities(countryCode, stateIndex) {
        const countryData = this.stateData[countryCode];
        if (!countryData || !countryData.states || !countryData.states[stateIndex]) return;
        
        const cities = countryData.states[stateIndex].cities;
        if (!cities || !cities.length) return;
        
        // Clear current options
        this.resetCitySelect();
        
        // Add cities
        cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city.code;
            option.textContent = city.name;
            this.citySelect.appendChild(option);
        });
        
        // Enable city select
        this.citySelect.disabled = false;
    }
    
    /**
     * Reset state select dropdown
     */
    resetStateSelect() {
        this.stateSelect.innerHTML = '<option value="">-- Pilih Provinsi/State --</option>';
        this.stateSelect.disabled = true;
    }
    
    /**
     * Reset city select dropdown
     */
    resetCitySelect() {
        this.citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
        this.citySelect.disabled = true;
    }
    
    /**
     * Update hidden input with combined location value
     */
    updateHiddenInput() {
        const countryCode = this.countrySelect.value;
        const stateCode = this.stateSelect.value;
        const cityCode = this.citySelect.value;
        
        const countryName = this.countrySelect.selectedOptions[0]?.textContent || '';
        const stateName = this.stateSelect.selectedOptions[0]?.textContent || '';
        const cityName = this.citySelect.selectedOptions[0]?.textContent || '';
        
        // Build location object
        const locationData = {
            country_code: countryCode,
            country_name: countryName,
            state_code: stateCode,
            state_name: stateName,
            city_code: cityCode,
            city_name: cityName,
            display: this.getDisplayValue(countryName, stateName, cityName)
        };
        
        // Update hidden input with JSON string
        this.hiddenInput.value = JSON.stringify(locationData);
    }
    
    /**
     * Get display value for location
     */
    getDisplayValue(country, state, city) {
        if (city && state && country) {
            return `${city}, ${state}, ${country}`;
        } else if (state && country) {
            return `${state}, ${country}`;
        } else if (country) {
            return country;
        }
        return '';
    }
    
    /**
     * Show error message in dropdown
     */
    showError(selectElement, message) {
        selectElement.innerHTML = '';
        const option = document.createElement('option');
        option.value = '';
        option.textContent = message;
        option.disabled = true;
        option.selected = true;
        selectElement.appendChild(option);
    }
    
    /**
     * Set initial value if available
     * @param {Object} locationData - Location data object
     */
    async setInitialValue(locationData) {
        if (!locationData) return;
        
        // Set country
        if (locationData.country_code) {
            this.countrySelect.value = locationData.country_code;
            
            // Fetch country data if needed
            if (!this.loadedCountries.includes(locationData.country_code)) {
                try {
                    await this.fetchStatesForCountry(locationData.country_code);
                    this.loadedCountries.push(locationData.country_code);
                } catch (error) {
                    console.error(`Failed to fetch initial states:`, error);
                    return;
                }
            }
            
            // Populate states and set state value
            this.populateStates(locationData.country_code);
            
            if (locationData.state_code) {
                this.stateSelect.value = locationData.state_code;
                
                // Find state index
                const stateIndex = Array.from(this.stateSelect.options)
                    .findIndex(option => option.value === locationData.state_code);
                
                if (stateIndex > 0) {
                    // Populate cities and set city value
                    this.populateCities(locationData.country_code, stateIndex - 1); // -1 because of the empty option
                    
                    if (locationData.city_code) {
                        this.citySelect.value = locationData.city_code;
                    }
                }
            }
            
            // Update hidden input
            this.updateHiddenInput();
        }
    }
}

// Make available globally
window.LocationHandler = LocationHandler;