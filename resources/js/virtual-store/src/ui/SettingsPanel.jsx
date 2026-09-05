import { usePlayerStore } from '../stores/playerStore.js';

export default function SettingsPanel() {
    const { settingsOpen, graphics, setQuality, toggleGraphics, toggleSettings } = usePlayerStore();
    if (!settingsOpen) return null;

    return (
        <aside className="vs-settings">
            <header>
                <div>
                    <span>Settings</span>
                    <h2>Graphics</h2>
                </div>
                <button type="button" onClick={toggleSettings}>Close</button>
            </header>
            <label>
                Quality
                <select value={graphics.quality} onChange={(event) => setQuality(event.target.value)}>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="ultra">Ultra</option>
                </select>
            </label>
            <label><input type="checkbox" checked={graphics.shadows} onChange={() => toggleGraphics('shadows')} /> Shadows</label>
            <label><input type="checkbox" checked={graphics.reflections} onChange={() => toggleGraphics('reflections')} /> Reflections</label>
            <label><input type="checkbox" checked={graphics.postProcessing} onChange={() => toggleGraphics('postProcessing')} /> Post-processing</label>
        </aside>
    );
}
