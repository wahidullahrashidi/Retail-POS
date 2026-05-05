@extends('layouts.app')

@push('styles')
    @vite(['resources/css/app.css'])
@endpush

@section('content')
    <div class="bk" x-data="backupPage()" x-init="init()">

        {{-- ════ TOPBAR ════ --}}
        <div class="bk-top">
            <div class="bk-title">Afghan <em>POS</em> — Backup & Sync</div>
            <div class="top-r">
                <button class="btn btn-ghost" @click="refreshAll()">
                    <i class="fas fa-rotate" :class="refreshing ? 'fa-spin' : ''"></i> Refresh
                </button>
            </div>
        </div>

        <div class="bk-body">

            {{-- ════ STATUS CARDS ════ --}}
            <div class="status-grid">
                <div class="status-card" style="--ac:var(--green)">
                    <div class="sc-label">Last Backup <span><i class="fas fa-clock" style="color:var(--green)"></i></span>
                    </div>
                    <div class="sc-val sm" x-text="status.last_backup || 'Never'"></div>
                    <div class="sc-sub">
                        <span class="status-dot" :class="status.last_backup ? 'dot-green' : 'dot-gray'"></span>
                        <span
                            x-text="status.last_backup_size ? status.last_backup_size + ' file size' : 'No backups yet'"></span>
                    </div>
                </div>
                <div class="status-card" style="--ac:var(--blue)">
                    <div class="sc-label">Cloud Sync <span><i class="fas fa-cloud" style="color:var(--blue)"></i></span>
                    </div>
                    <div class="sc-val sm" x-text="status.cloud_status || 'Not configured'"></div>
                    <div class="sc-sub">
                        <span class="status-dot" :class="status.cloud_enabled ? 'dot-green' : 'dot-gray'"></span>
                        <span x-text="status.cloud_provider || 'No provider set'"></span>
                    </div>
                </div>
                <div class="status-card" style="--ac:var(--amber)">
                    <div class="sc-label">Pending Sync <span><i class="fas fa-rotate" style="color:var(--amber)"></i></span>
                    </div>
                    <div class="sc-val" style="color:var(--amber)" x-text="status.total_pending || 0"></div>
                    <div class="sc-sub">
                        <span class="status-dot" :class="(status.total_pending || 0) > 0 ? 'dot-amber' : 'dot-green'"></span>
                        <span
                            x-text="(status.total_pending||0) > 0 ? 'records awaiting sync' : 'all records synced'"></span>
                    </div>
                </div>
                <div class="status-card" style="--ac:var(--violet)">
                    <div class="sc-label">Disk Usage <span><i class="fas fa-hard-drive"
                                style="color:var(--violet)"></i></span></div>
                    <div class="sc-val sm" x-text="status.disk_used || '—'"></div>
                    <div class="sc-sub">
                        <span class="status-dot" :class="(status.disk_pct || 0) > 85 ? 'dot-red' : 'dot-green'"></span>
                        <span x-text="(status.disk_pct||0) + '% of ' + (status.disk_total||'—') + ' used'"></span>
                    </div>
                </div>
            </div>

            {{-- ════ BACKUP NOW HERO ════ --}}
            <div class="backup-hero">
                <div class="bh-left">
                    <div class="bh-title">Run a Backup</div>
                    <div class="bh-sub">
                        Creates a compressed archive of your entire database and stores it locally.
                        If cloud sync is configured, it will also upload to your cloud provider.
                    </div>
                    <div class="bh-meta">
                        <div class="bh-meta-item">
                            <i class="fas fa-database"></i>
                            <span>Database: <strong x-text="status.db_name || 'afghan_pos'"></strong></span>
                        </div>
                        <div class="bh-meta-item">
                            <i class="fas fa-folder"></i>
                            <span>Stored in: <strong x-text="status.backup_path || 'storage/backups'"></strong></span>
                        </div>
                        <div class="bh-meta-item">
                            <i class="fas fa-shield"></i>
                            <span>Encrypted: <strong x-text="status.encrypted ? 'Yes' : 'No'"></strong></span>
                        </div>
                    </div>

                    {{-- Progress --}}
                    <div class="backup-progress" x-show="backupRunning || backupDone" x-cloak>
                        <div class="bp-label">
                            <span x-text="backupStepLabel"></span>
                            <strong x-text="backupPct + '%'"></strong>
                        </div>
                        <div class="bp-bar">
                            <div class="bp-fill" :class="backupFailed ? 'error' : backupDone ? 'success' : ''"
                                :style="`width:${backupPct}%`"></div>
                        </div>
                        <div class="bp-steps">
                            <template x-for="step in backupSteps" :key="step.label">
                                <div class="bp-step" :class="step.state">
                                    <i
                                        :class="{
                                            'fas fa-check-circle': step.state==='done',
                                            'fas fa-circle-notch fa-spin': step.state==='active',
                                            'fas fa-circle': step.state==='pending',
                                            'fas fa-times-circle': step.state==='failed',
                                        }"></i>
                                    <span x-text="step.label"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="bh-right">
                    <button type="button" class="btn-backup-now" @click="runBackup()" :disabled="backupRunning">
                        <template x-if="!backupRunning">
                            <span style="display:flex;align-items:center;gap:8px">
                                <i class="fas fa-cloud-arrow-up"></i> Backup Now
                            </span>
                        </template>
                        <template x-if="backupRunning">
                            <span style="display:flex;align-items:center;gap:8px">
                                <i class="fas fa-spinner fa-spin"></i> Running…
                            </span>
                        </template>
                    </button>
                    <button type="button" class="btn btn-ghost" style="justify-content:center" @click="runSync()"
                        :disabled="syncRunning">
                        <i class="fas fa-rotate" :class="syncRunning ? 'fa-spin' : ''"></i>
                        <span x-text="syncRunning?'Syncing…':'Sync Records'"></span>
                    </button>
                </div>
            </div>

            <div class="grid-3-2">

                {{-- ════ LEFT COL ════ --}}
                <div style="display:flex;flex-direction:column;gap:1.25rem">

                    {{-- BACKUP HISTORY --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-history"></i> Backup History</div>
                            <span style="font-size:11px;color:var(--ink3)"
                                x-text="backups.length + ' backup(s) stored'"></span>
                        </div>
                        <div x-show="backupsLoading" style="text-align:center;padding:2rem;color:var(--ink3)">
                            <i class="fas fa-spinner fa-spin" style="font-size:18px"></i>
                        </div>
                        <div x-show="!backupsLoading">
                            <div class="empty-state" x-show="backups.length===0">
                                <i class="fas fa-folder-open"></i>
                                <p>No backups yet.<br>Run your first backup above.</p>
                            </div>
                            <div x-show="backups.length>0" style="overflow-x:auto">
                                <table class="bk-table">
                                    <thead>
                                        <tr>
                                            <th>File Name</th>
                                            <th>Size</th>
                                            <th>Created</th>
                                            <th>Type</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="b in backups" :key="b.path">
                                            <tr>
                                                <td><span class="bk-file" x-text="b.name"></span></td>
                                                <td><span class="bk-size" x-text="b.size"></span></td>
                                                <td><span class="bk-date" x-text="b.created_at"></span></td>
                                                <td>
                                                    <span class="pill" :class="b.cloud ? 'pill-blue' : 'pill-gray'"
                                                        x-text="b.cloud ? 'Cloud + Local' : 'Local'"></span>
                                                </td>
                                                <td>
                                                    <div class="row-acts">
                                                        <button type="button" class="btn btn-ghost btn-sm"
                                                            @click="downloadBackup(b)" title="Download">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-green btn-sm"
                                                            @click="openRestoreModal(b)" title="Restore">
                                                            <i class="fas fa-rotate-left"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            @click="deleteBackup(b)" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- SYNC STATUS --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-rotate"></i> Sync Status by Table</div>
                            <button type="button" class="btn btn-ghost btn-sm" @click="runSync()"
                                :disabled="syncRunning">
                                <i class="fas fa-rotate" :class="syncRunning ? 'fa-spin' : ''"></i>
                                <span x-text="syncRunning?'Syncing…':'Sync All'"></span>
                            </button>
                        </div>
                        <div class="card-body">
                            <template x-for="table in syncTables" :key="table.name">
                                <div class="sync-row">
                                    <div class="sr-left">
                                        <div class="sr-icon" :style="`background:${table.color}20;color:${table.color}`">
                                            <i :class="table.icon"></i>
                                        </div>
                                        <div>
                                            <div class="sr-name" x-text="table.label"></div>
                                            <div class="sr-count" x-text="table.total + ' total records'"></div>
                                        </div>
                                    </div>
                                    <div class="sr-right">
                                        <span class="pill" :class="table.failed > 0 ? 'pill-red' : 'pill-gray'"
                                            x-show="table.failed > 0" x-text="table.failed + ' failed'"></span>
                                        <div style="text-align:right">
                                            <div class="sr-pending" :class="table.pending > 0 ? 'has' : 'none'"
                                                x-text="table.pending > 0 ? table.pending + ' pending' : '✓ Synced'"></div>
                                        </div>
                                        <button type="button" class="btn btn-ghost btn-sm"
                                            x-show="table.pending > 0 || table.failed > 0" @click="syncTable(table.name)">
                                            <i class="fas fa-rotate"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- BACKUP LOG --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-terminal"></i> Activity Log</div>
                            <button type="button" class="btn btn-ghost btn-sm" @click="clearLog()">
                                <i class="fas fa-trash"></i> Clear
                            </button>
                        </div>
                        <div class="card-body">
                            <div x-show="logs.length===0" class="empty-state" style="padding:1.5rem">
                                <i class="fas fa-file-lines" style="font-size:24px"></i>
                                <p>No activity yet.</p>
                            </div>
                            <div class="log-list">
                                <template x-for="(log, idx) in logs" :key="idx">
                                    <div class="log-item" :class="log.type">
                                        <i class="log-icon"
                                            :class="{
                                                'fas fa-check-circle': log.type==='success',
                                                'fas fa-times-circle': log.type==='error',
                                                'fas fa-info-circle': log.type==='info',
                                                'fas fa-triangle-exclamation': log.type==='warning',
                                            }"></i>
                                        <span class="log-text" x-text="log.message"></span>
                                        <span class="log-time" x-text="log.time"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ════ RIGHT COL ════ --}}
                <div style="display:flex;flex-direction:column;gap:1.25rem">

                    {{-- SCHEDULE --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-clock"></i> Backup Schedule</div>
                            <button type="button" class="btn btn-primary btn-sm" @click="saveSchedule()">
                                <i class="fas fa-floppy-disk"></i> Save
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="schedule-grid">
                                <div class="schedule-item">
                                    <div>
                                        <div class="si-label"><i class="fas fa-clock"></i> Daily Backup</div>
                                        <div class="si-sub">Runs every day at set time</div>
                                    </div>
                                    <div class="si-right">
                                        <select class="f-sel" x-model="schedule.daily_time" style="width:100px">
                                            <option value="00:00">Midnight</option>
                                            <option value="02:00">2:00 AM</option>
                                            <option value="06:00">6:00 AM</option>
                                            <option value="12:00">Noon</option>
                                            <option value="22:00">10:00 PM</option>
                                            <option value="23:00">11:00 PM</option>
                                        </select>
                                        <label class="toggle">
                                            <input type="checkbox" x-model="schedule.daily_enabled">
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="schedule-item">
                                    <div>
                                        <div class="si-label"><i class="fas fa-calendar-week"></i> Weekly Backup</div>
                                        <div class="si-sub">Full backup every week</div>
                                    </div>
                                    <label class="toggle">
                                        <input type="checkbox" x-model="schedule.weekly_enabled">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="schedule-item">
                                    <div>
                                        <div class="si-label"><i class="fas fa-cloud-arrow-up"></i> Auto Cloud Upload
                                        </div>
                                        <div class="si-sub">Upload after each backup</div>
                                    </div>
                                    <label class="toggle">
                                        <input type="checkbox" x-model="schedule.auto_cloud">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="schedule-item">
                                    <div>
                                        <div class="si-label"><i class="fas fa-broom"></i> Auto Cleanup</div>
                                        <div class="si-sub">Keep only last N backups</div>
                                    </div>
                                    <div class="si-right">
                                        <select class="f-sel" x-model="schedule.keep_count" style="width:80px">
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="30">30</option>
                                            <option value="0">All</option>
                                        </select>
                                        <label class="toggle">
                                            <input type="checkbox" x-model="schedule.cleanup_enabled">
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="schedule-item">
                                    <div>
                                        <div class="si-label"><i class="fas fa-lock"></i> Encrypt Backups</div>
                                        <div class="si-sub">AES-256 encryption</div>
                                    </div>
                                    <label class="toggle">
                                        <input type="checkbox" x-model="schedule.encrypt">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DISK USAGE --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-hard-drive"></i> Storage</div>
                        </div>
                        <div class="card-body">
                            <div class="disk-gauge">
                                <div class="dg-row">
                                    <span>Local Disk</span>
                                    <span style="font-family:var(--mono);font-size:12px"
                                        x-text="status.disk_used + ' / ' + status.disk_total"></span>
                                </div>
                                <div class="dg-bar">
                                    <div class="dg-fill"
                                        :style="`width:${status.disk_pct||0}%;background:${(status.disk_pct||0)>85?'var(--red)':(status.disk_pct||0)>60?'var(--amber)':'var(--blue)'}`">
                                    </div>
                                </div>
                                <div class="dg-legend">
                                    <div class="dg-leg">
                                        <div class="dg-dot" style="background:var(--blue)"></div> Used
                                    </div>
                                    <div class="dg-leg">
                                        <div class="dg-dot" style="background:var(--s3)"></div> Free
                                    </div>
                                </div>
                                <div
                                    style="margin-top:.75rem;padding:.75rem;background:var(--s2);border:1px solid var(--border);border-radius:var(--rsm)">
                                    <div
                                        style="display:flex;justify-content:space-between;font-size:12px;color:var(--ink2)">
                                        <span>Backup folder size</span>
                                        <span style="font-family:var(--mono);font-weight:600"
                                            x-text="status.backup_folder_size || '—'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CLOUD CONFIG --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-cloud"></i> Cloud Configuration</div>
                            <button type="button" class="btn btn-primary btn-sm" @click="saveCloudConfig()">
                                <i class="fas fa-floppy-disk"></i> Save
                            </button>
                        </div>
                        <div class="card-body">

                            <div class="cloud-providers">
                                <button type="button" class="cloud-btn"
                                    :class="cloudConfig.provider === 'gdrive' ? 'active' : ''"
                                    @click="cloudConfig.provider='gdrive'">
                                    <span class="cloud-btn-icon">🗂️</span>
                                    <div class="cloud-btn-label">Google Drive</div>
                                    <div class="cloud-btn-sub">Free 15GB</div>
                                </button>
                                <button type="button" class="cloud-btn"
                                    :class="cloudConfig.provider === 'dropbox' ? 'active' : ''"
                                    @click="cloudConfig.provider='dropbox'">
                                    <span class="cloud-btn-icon">📦</span>
                                    <div class="cloud-btn-label">Dropbox</div>
                                    <div class="cloud-btn-sub">Free 2GB</div>
                                </button>
                                <button type="button" class="cloud-btn"
                                    :class="cloudConfig.provider === 'ftp' ? 'active' : ''" @click="cloudConfig.provider='ftp'">
                                    <span class="cloud-btn-icon">🖥️</span>
                                    <div class="cloud-btn-label">FTP Server</div>
                                    <div class="cloud-btn-sub">Custom server</div>
                                </button>
                            </div>

                            {{-- Google Drive fields --}}
                            <div x-show="cloudConfig.provider==='gdrive'" x-cloak>
                                <div class="form-grid">
                                    <div>
                                        <label class="field-label">Service Account JSON Path</label>
                                        <input type="text" class="field-input" x-model="cloudConfig.gdrive_key"
                                            placeholder="storage/google-service-account.json">
                                        <div class="field-hint">Path to your Google service account credentials file</div>
                                    </div>
                                    <div>
                                        <label class="field-label">Drive Folder ID</label>
                                        <input type="text" class="field-input" x-model="cloudConfig.gdrive_folder"
                                            placeholder="1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs">
                                    </div>
                                </div>
                                <div class="config-env" style="margin-top:.75rem">
                                    <div class="env-title">Add to your .env file</div>
                                    <div class="env-line">FILESYSTEM_CLOUD=<span>google</span></div>
                                    <div class="env-line">GOOGLE_DRIVE_CLIENT_ID=<span>your_client_id</span></div>
                                    <div class="env-line">GOOGLE_DRIVE_CLIENT_SECRET=<span>your_secret</span></div>
                                    <div class="env-line">GOOGLE_DRIVE_REFRESH_TOKEN=<span>your_token</span></div>
                                </div>
                            </div>

                            {{-- Dropbox fields --}}
                            <div x-show="cloudConfig.provider==='dropbox'" x-cloak>
                                <div class="form-grid">
                                    <div>
                                        <label class="field-label">Access Token</label>
                                        <input type="password" class="field-input" x-model="cloudConfig.dropbox_token"
                                            placeholder="sl.xxxxxx…">
                                    </div>
                                    <div>
                                        <label class="field-label">Backup Folder Path</label>
                                        <input type="text" class="field-input" x-model="cloudConfig.dropbox_path"
                                            placeholder="/afghan-pos-backups">
                                    </div>
                                </div>
                                <div class="config-env" style="margin-top:.75rem">
                                    <div class="env-title">Add to your .env file</div>
                                    <div class="env-line">FILESYSTEM_CLOUD=<span>dropbox</span></div>
                                    <div class="env-line">DROPBOX_AUTH_TOKEN=<span>your_access_token</span></div>
                                </div>
                            </div>

                            {{-- FTP fields --}}
                            <div x-show="cloudConfig.provider==='ftp'" x-cloak>
                                <div class="form-grid form-2">
                                    <div>
                                        <label class="field-label">Host</label>
                                        <input type="text" class="field-input" x-model="cloudConfig.ftp_host"
                                            placeholder="ftp.example.com">
                                    </div>
                                    <div>
                                        <label class="field-label">Port</label>
                                        <input type="number" class="field-input" x-model="cloudConfig.ftp_port"
                                            placeholder="21">
                                    </div>
                                    <div>
                                        <label class="field-label">Username</label>
                                        <input type="text" class="field-input" x-model="cloudConfig.ftp_user"
                                            placeholder="ftpuser">
                                    </div>
                                    <div>
                                        <label class="field-label">Password</label>
                                        <input type="password" class="field-input" x-model="cloudConfig.ftp_pass"
                                            placeholder="••••••••">
                                    </div>
                                    <div style="grid-column:span 2">
                                        <label class="field-label">Remote Path</label>
                                        <input type="text" class="field-input" x-model="cloudConfig.ftp_path"
                                            placeholder="/backups/afghan-pos">
                                    </div>
                                </div>
                                <div class="config-env" style="margin-top:.75rem">
                                    <div class="env-title">Add to your .env file</div>
                                    <div class="env-line">FILESYSTEM_CLOUD=<span>ftp</span></div>
                                    <div class="env-line">FTP_HOST=<span>your_ftp_host</span></div>
                                    <div class="env-line">FTP_USERNAME=<span>your_username</span></div>
                                    <div class="env-line">FTP_PASSWORD=<span>your_password</span></div>
                                </div>
                            </div>

                            {{-- Test connection --}}
                            <div style="display:flex;gap:8px;margin-top:1rem">
                                <button type="button" class="btn btn-ghost" style="flex:1"
                                    @click="testCloudConnection()">
                                    <i class="fas fa-plug"></i> Test Connection
                                </button>
                            </div>
                            <div x-show="cloudTestResult" x-cloak
                                style="margin-top:8px;padding:9px 12px;border-radius:var(--rsm);font-size:12px"
                                :style="cloudTestOk ?
                                    'background:var(--gdim);border:1px solid rgba(22,163,74,.2);color:var(--green)' :
                                    'background:var(--rdim);border:1px solid rgba(220,38,38,.2);color:var(--red)'"
                                x-text="cloudTestResult"></div>
                        </div>
                    </div>

                </div>
            </div>

        </div>{{-- /bk-body --}}

        {{-- ════ RESTORE MODAL ════ --}}
        <div class="modal-overlay" x-show="showRestoreModal" x-cloak @click.self="showRestoreModal=false">
            <div class="modal-card">
                <div class="modal-head">
                    <div class="modal-title">Restore Backup</div>
                    <button class="modal-close" @click="showRestoreModal=false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="warn-box">
                        <i class="fas fa-triangle-exclamation" style="flex-shrink:0;margin-top:1px"></i>
                        <div>
                            <strong>Warning — this will overwrite your current database.</strong><br>
                            All data created after this backup was made will be permanently lost.
                            We strongly recommend running a fresh backup before restoring.
                        </div>
                    </div>
                    <div class="restore-file-info">
                        <div class="rfi-row"><span>File</span><span class="rfi-val" x-text="restoreTarget?.name"></span>
                        </div>
                        <div class="rfi-row"><span>Size</span><span class="rfi-val" x-text="restoreTarget?.size"></span>
                        </div>
                        <div class="rfi-row"><span>Created</span><span class="rfi-val"
                                x-text="restoreTarget?.created_at"></span></div>
                        <div class="rfi-row"><span>Type</span><span class="rfi-val"
                                x-text="restoreTarget?.cloud ? 'Cloud + Local' : 'Local'"></span></div>
                    </div>
                    <div style="margin-bottom:.75rem">
                        <label class="field-label">Type <strong
                                style="font-family:var(--mono);color:var(--red)">RESTORE</strong> to confirm</label>
                        <input type="text" class="field-input" x-model="restoreConfirmText" placeholder="RESTORE">
                    </div>
                    <div x-show="restoreError" x-cloak
                        style="padding:9px 12px;background:var(--rdim);border:1px solid rgba(220,38,38,.2);border-radius:var(--rsm);font-size:12px;color:var(--red)"
                        x-text="restoreError"></div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" @click="showRestoreModal=false">Cancel</button>
                    <button type="button" class="btn btn-danger" @click="confirmRestore()"
                        :disabled="restoreConfirmText !== 'RESTORE' || restoreSaving">
                        <i class="fas fa-spinner fa-spin" x-show="restoreSaving"></i>
                        <span x-text="restoreSaving ? 'Restoring…' : 'Restore Database'"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>{{-- /bk --}}
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('backupPage', () => ({

                /* state */
                status: {},
                backups: [],
                syncTables: [],
                logs: [],
                backupsLoading: true,
                refreshing: false,
                backupRunning: false,
                backupDone: false,
                backupFailed: false,
                backupPct: 0,
                backupStepLabel: '',
                syncRunning: false,

                /* backup steps */
                backupSteps: [{
                        label: 'Connecting to DB',
                        state: 'pending'
                    },
                    {
                        label: 'Dumping database',
                        state: 'pending'
                    },
                    {
                        label: 'Compressing',
                        state: 'pending'
                    },
                    {
                        label: 'Saving locally',
                        state: 'pending'
                    },
                    {
                        label: 'Uploading to cloud',
                        state: 'pending'
                    },
                ],

                /* schedule */
                schedule: {
                    daily_enabled: true,
                    daily_time: '02:00',
                    weekly_enabled: true,
                    auto_cloud: false,
                    cleanup_enabled: true,
                    keep_count: '10',
                    encrypt: false,
                },

                /* cloud */
                cloudConfig: {
                    provider: 'gdrive',
                    gdrive_key: '',
                    gdrive_folder: '',
                    dropbox_token: '',
                    dropbox_path: '/afghan-pos-backups',
                    ftp_host: '',
                    ftp_port: '21',
                    ftp_user: '',
                    ftp_pass: '',
                    ftp_path: '/backups',
                },
                cloudTestResult: '',
                cloudTestOk: false,

                /* restore */
                showRestoreModal: false,
                restoreTarget: null,
                restoreConfirmText: '',
                restoreError: '',
                restoreSaving: false,

                /* urls */
                urls: {
                    status: '{{ route('pos.backup.status') }}',
                    backups: '{{ route('pos.backup.list') }}',
                    run: '{{ route('pos.backup.run') }}',
                    restore: '{{ route('pos.backup.restore') }}',
                    delete: '{{ route('pos.backup.delete') }}',
                    sync: '{{ route('pos.backup.sync') }}',
                    schedule: '{{ route('pos.backup.schedule') }}',
                    cloud: '{{ route('pos.backup.cloud') }}',
                    cloudTest: '{{ route('pos.backup.cloud.test') }}',
                    csrf: document.querySelector('meta[name=csrf-token]').content,
                },

                /* ── Init ── */
                async init() {
                    await this.loadStatus();
                    await this.loadBackups();
                    this.loadSchedule();
                },

                async refreshAll() {
                    this.refreshing = true;
                    await this.loadStatus();
                    await this.loadBackups();
                    this.refreshing = false;
                },

                /* ── Load status ── */
                async loadStatus() {
                    try {
                        const r = await fetch(this.urls.status, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const d = await r.json();
                        this.status = d.status;
                        this.syncTables = d.sync_tables;
                    } catch (e) {
                        this.addLog('error', 'Failed to load status: ' + e.message);
                    }
                },

                /* ── Load backup list ── */
                async loadBackups() {
                    this.backupsLoading = true;
                    try {
                        const r = await fetch(this.urls.backups, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        this.backups = await r.json();
                    } catch (e) {
                        this.addLog('error', 'Failed to load backups: ' + e.message);
                    } finally {
                        this.backupsLoading = false;
                    }
                },

                /* ── Run backup ── */
                async runBackup() {
                    this.backupRunning = true;
                    this.backupDone = false;
                    this.backupFailed = false;
                    this.backupPct = 0;
                    this.backupSteps = this.backupSteps.map(s => ({
                        ...s,
                        state: 'pending'
                    }));
                    this.addLog('info', 'Backup started…');

                    // Animate steps
                    const steps = [{
                            idx: 0,
                            pct: 15,
                            label: 'Connecting to database…'
                        },
                        {
                            idx: 1,
                            pct: 35,
                            label: 'Dumping database…'
                        },
                        {
                            idx: 2,
                            pct: 60,
                            label: 'Compressing archive…'
                        },
                        {
                            idx: 3,
                            pct: 80,
                            label: 'Saving locally…'
                        },
                        {
                            idx: 4,
                            pct: 95,
                            label: 'Uploading to cloud…'
                        },
                    ];

                    try {
                        // Run fake progress while waiting for server
                        let stepIdx = 0;
                        const progressInterval = setInterval(() => {
                            if (stepIdx < steps.length) {
                                const s = steps[stepIdx];
                                if (stepIdx > 0) this.backupSteps[stepIdx - 1].state =
                                    'done';
                                this.backupSteps[stepIdx].state = 'active';
                                this.backupPct = s.pct;
                                this.backupStepLabel = s.label;
                                stepIdx++;
                            }
                        }, 800);

                        const r = await fetch(this.urls.run, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                        });
                        const d = await r.json();

                        clearInterval(progressInterval);

                        if (d.success) {
                            this.backupSteps = this.backupSteps.map(s => ({
                                ...s,
                                state: 'done'
                            }));
                            this.backupPct = 100;
                            this.backupStepLabel = 'Backup complete!';
                            this.backupDone = true;
                            this.addLog('success', `Backup completed: ${d.filename} (${d.size})`);
                            await this.loadBackups();
                            await this.loadStatus();
                        } else {
                            throw new Error(d.message);
                        }
                    } catch (e) {
                        this.backupFailed = true;
                        this.backupStepLabel = 'Backup failed: ' + e.message;
                        this.backupSteps = this.backupSteps.map(s => s.state === 'active' ? {
                            ...s,
                            state: 'failed'
                        } : s);
                        this.addLog('error', 'Backup failed: ' + e.message);
                    } finally {
                        this.backupRunning = false;
                    }
                },

                /* ── Sync all tables ── */
                async runSync() {
                    this.syncRunning = true;
                    this.addLog('info', 'Starting record sync…');
                    try {
                        const r = await fetch(this.urls.sync, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                table: 'all'
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.addLog('success', `Sync complete. ${d.synced} records synced.`);
                            await this.loadStatus();
                        } else {
                            this.addLog('error', 'Sync failed: ' + d.message);
                        }
                    } catch (e) {
                        this.addLog('error', 'Sync error: ' + e.message);
                    } finally {
                        this.syncRunning = false;
                    }
                },

                async syncTable(tableName) {
                    this.addLog('info', `Syncing ${tableName}…`);
                    try {
                        const r = await fetch(this.urls.sync, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                table: tableName
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.addLog('success', `${tableName}: ${d.synced} records synced.`);
                            await this.loadStatus();
                        } else {
                            this.addLog('error', `${tableName} sync failed: ${d.message}`);
                        }
                    } catch (e) {
                        this.addLog('error', 'Error: ' + e.message);
                    }
                },

                /* ── Restore ── */
                openRestoreModal(b) {
                    this.restoreTarget = b;
                    this.restoreConfirmText = '';
                    this.restoreError = '';
                    this.showRestoreModal = true;
                },

                async confirmRestore() {
                    if (this.restoreConfirmText !== 'RESTORE') return;
                    this.restoreSaving = true;
                    this.restoreError = '';
                    this.addLog('warning', 'Restore started — this will overwrite the database…');
                    try {
                        const r = await fetch(this.urls.restore, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                path: this.restoreTarget.path
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.showRestoreModal = false;
                            this.addLog('success', 'Database restored successfully from ' + this
                                .restoreTarget.name);
                        } else {
                            this.restoreError = d.message ?? 'Restore failed.';
                            this.addLog('error', 'Restore failed: ' + d.message);
                        }
                    } catch (e) {
                        this.restoreError = 'Network error: ' + e.message;
                    } finally {
                        this.restoreSaving = false;
                    }
                },

                /* ── Download backup ── */
                downloadBackup(b) {
                    window.location.href = '{{ url('pos/backup/download') }}?path=' +
                        encodeURIComponent(b.path);
                },

                /* ── Delete backup ── */
                async deleteBackup(b) {
                    if (!confirm(`Delete backup "${b.name}"? This cannot be undone.`)) return;
                    try {
                        const r = await fetch(this.urls.delete, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                path: b.path
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.addLog('info', 'Backup deleted: ' + b.name);
                            this.loadBackups();
                        }
                    } catch (e) {
                        this.addLog('error', 'Delete failed: ' + e.message);
                    }
                },

                /* ── Schedule ── */
                loadSchedule() {
                    const saved = localStorage.getItem('backup_schedule');
                    if (saved) this.schedule = {
                        ...this.schedule,
                        ...JSON.parse(saved)
                    };
                },

                async saveSchedule() {
                    localStorage.setItem('backup_schedule', JSON.stringify(this.schedule));
                    try {
                        await fetch(this.urls.schedule, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify(this.schedule)
                        });
                    } catch (e) {}
                    this.addLog('success', 'Schedule settings saved.');
                },

                /* ── Cloud config ── */
                async saveCloudConfig() {
                    try {
                        const r = await fetch(this.urls.cloud, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify(this.cloudConfig)
                        });
                        const d = await r.json();
                        if (d.success) this.addLog('success', 'Cloud configuration saved.');
                        else this.addLog('error', d.message);
                    } catch (e) {
                        this.addLog('error', 'Failed to save cloud config.');
                    }
                },

                async testCloudConnection() {
                    this.cloudTestResult = 'Testing connection…';
                    this.cloudTestOk = false;
                    try {
                        const r = await fetch(this.urls.cloudTest, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                provider: this.cloudConfig.provider
                            })
                        });
                        const d = await r.json();
                        this.cloudTestOk = d.success;
                        this.cloudTestResult = d.message;
                    } catch (e) {
                        this.cloudTestOk = false;
                        this.cloudTestResult = 'Connection test failed: ' + e.message;
                    }
                },

                /* ── Log ── */
                addLog(type, message) {
                    this.logs.unshift({
                        type,
                        message,
                        time: new Date().toLocaleTimeString('en-GB'),
                    });
                    if (this.logs.length > 50) this.logs.pop();
                },

                clearLog() {
                    this.logs = [];
                },
            }));
        });
    </script>
@endpush
