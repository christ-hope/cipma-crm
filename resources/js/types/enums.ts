export enum ApplicationStatus {
    SUBMITTED      = 'submitted',
    UNDER_REVIEW   = 'under_review',
    APPROVED       = 'approved',
    REJECTED       = 'rejected',
    INFO_REQUESTED = 'info_requested',
}

export const ApplicationStatusLabels: Record<ApplicationStatus, string> = {
    [ApplicationStatus.SUBMITTED]:      'Soumise',
    [ApplicationStatus.UNDER_REVIEW]:   'En révision',
    [ApplicationStatus.APPROVED]:       'Approuvée',
    [ApplicationStatus.REJECTED]:       'Rejetée',
    [ApplicationStatus.INFO_REQUESTED]: 'Informations demandées',
}

export const ApplicationStatusColors: Record<ApplicationStatus, string> = {
    [ApplicationStatus.SUBMITTED]:      'blue',
    [ApplicationStatus.UNDER_REVIEW]:   'yellow',
    [ApplicationStatus.APPROVED]:       'green',
    [ApplicationStatus.REJECTED]:       'red',
    [ApplicationStatus.INFO_REQUESTED]: 'orange',
}

// ─────────────────────────────────────────────────────────────────────────────

export enum DocumentType {
    CV         = 'cv',
    DIPLOME    = 'diplome',
    PHOTO      = 'photo',
    MOTIVATION = 'motivation',
    ID_CARD    = 'id_card',
    TRANSCRIPT = 'transcript',
    OTHER      = 'other',
}

export const DocumentTypeLabels: Record<DocumentType, string> = {
    [DocumentType.CV]:         'Curriculum Vitae',
    [DocumentType.DIPLOME]:    'Diplôme',
    [DocumentType.PHOTO]:      "Photo d'identité",
    [DocumentType.MOTIVATION]: 'Lettre de motivation',
    [DocumentType.ID_CARD]:    "Pièce d'identité",
    [DocumentType.TRANSCRIPT]: 'Relevé de notes',
    [DocumentType.OTHER]:      'Autre document',
}

export const RequiredDocuments = [DocumentType.CV, DocumentType.DIPLOME, DocumentType.PHOTO]

// ─────────────────────────────────────────────────────────────────────────────
// DOMAIN: Student
// ─────────────────────────────────────────────────────────────────────────────

export enum StudentStatus {
    ACTIVE    = 'active',
    SUSPENDED = 'suspended',
    GRADUATED = 'graduated',
    WITHDRAWN = 'withdrawn',
}

export const StudentStatusLabels: Record<StudentStatus, string> = {
    [StudentStatus.ACTIVE]:    'Actif',
    [StudentStatus.SUSPENDED]: 'Suspendu',
    [StudentStatus.GRADUATED]: 'Diplômé',
    [StudentStatus.WITHDRAWN]: 'Retiré',
}

export const StudentStatusColors: Record<StudentStatus, string> = {
    [StudentStatus.ACTIVE]:    'green',
    [StudentStatus.SUSPENDED]: 'yellow',
    [StudentStatus.GRADUATED]: 'blue',
    [StudentStatus.WITHDRAWN]: 'red',
}

// ─────────────────────────────────────────────────────────────────────────────
// DOMAIN: Academic
// ─────────────────────────────────────────────────────────────────────────────

export enum EvaluationMode {
    CRM      = 'crm',
    EXTERNAL = 'external',
    MANUAL   = 'manual',
}

export const EvaluationModeLabels: Record<EvaluationMode, string> = {
    [EvaluationMode.CRM]:      'Géré dans le CRM',
    [EvaluationMode.EXTERNAL]: 'Évaluation externe (Moodle)',
    [EvaluationMode.MANUAL]:   'Saisie manuelle',
}

// ─────────────────────────────────────────────────────────────────────────────

export enum FormationMode {
    ONLINE     = 'online',
    PRESENTIEL = 'presentiel',
    HYBRID     = 'hybrid',
}

export const FormationModeLabels: Record<FormationMode, string> = {
    [FormationMode.ONLINE]:     'En ligne',
    [FormationMode.PRESENTIEL]: 'Présentiel',
    [FormationMode.HYBRID]:     'Hybride',
}

export const FormationModeIcons: Record<FormationMode, string> = {
    [FormationMode.ONLINE]:     '💻',
    [FormationMode.PRESENTIEL]: '🏫',
    [FormationMode.HYBRID]:     '🔀',
}

// ─────────────────────────────────────────────────────────────────────────────

export enum ClassStatus {
    PLANNED             = 'planned',
    REGISTRATION_OPEN   = 'registration_open',
    REGISTRATION_CLOSED = 'registration_closed',
    IN_PROGRESS         = 'in_progress',
    COMPLETED           = 'completed',
    CANCELLED           = 'cancelled',
}

export const ClassStatusLabels: Record<ClassStatus, string> = {
    [ClassStatus.PLANNED]:             'Planifiée',
    [ClassStatus.REGISTRATION_OPEN]:   'Inscriptions ouvertes',
    [ClassStatus.REGISTRATION_CLOSED]: 'Inscriptions fermées',
    [ClassStatus.IN_PROGRESS]:         'En cours',
    [ClassStatus.COMPLETED]:           'Terminée',
    [ClassStatus.CANCELLED]:           'Annulée',
}

export const ClassStatusColors: Record<ClassStatus, string> = {
    [ClassStatus.PLANNED]:             'gray',
    [ClassStatus.REGISTRATION_OPEN]:   'green',
    [ClassStatus.REGISTRATION_CLOSED]: 'yellow',
    [ClassStatus.IN_PROGRESS]:         'blue',
    [ClassStatus.COMPLETED]:           'purple',
    [ClassStatus.CANCELLED]:           'red',
}

// ─────────────────────────────────────────────────────────────────────────────

export enum EnrollmentStatus {
    PENDING   = 'pending',
    ACTIVE    = 'active',
    COMPLETED = 'completed',
    WITHDRAWN = 'withdrawn',
    FAILED    = 'failed',
}

export const EnrollmentStatusLabels: Record<EnrollmentStatus, string> = {
    [EnrollmentStatus.PENDING]:   'En attente de paiement',
    [EnrollmentStatus.ACTIVE]:    'Actif',
    [EnrollmentStatus.COMPLETED]: 'Terminé',
    [EnrollmentStatus.WITHDRAWN]: 'Abandon',
    [EnrollmentStatus.FAILED]:    'Échec',
}

export const EnrollmentStatusColors: Record<EnrollmentStatus, string> = {
    [EnrollmentStatus.PENDING]:   'yellow',
    [EnrollmentStatus.ACTIVE]:    'green',
    [EnrollmentStatus.COMPLETED]: 'blue',
    [EnrollmentStatus.WITHDRAWN]: 'orange',
    [EnrollmentStatus.FAILED]:    'red',
}

// ─────────────────────────────────────────────────────────────────────────────

export enum EvaluationSource {
    CRM    = 'crm',
    MOODLE = 'moodle',
    MANUAL = 'manual',
}

export const EvaluationSourceLabels: Record<EvaluationSource, string> = {
    [EvaluationSource.CRM]:    'CRM (formation interne)',
    [EvaluationSource.MOODLE]: 'Moodle',
    [EvaluationSource.MANUAL]: 'Saisie manuelle',
}

export const EvaluationSourceIcons: Record<EvaluationSource, string> = {
    [EvaluationSource.CRM]:    '🖥️',
    [EvaluationSource.MOODLE]: '📚',
    [EvaluationSource.MANUAL]: '✏️',
}

// ─────────────────────────────────────────────────────────────────────────────

export enum EvaluationType {
    EXAM       = 'exam',
    QUIZ       = 'quiz',
    PROJECT    = 'project',
    PRACTICAL  = 'practical',
    ASSIGNMENT = 'assignment',
}

export const EvaluationTypeLabels: Record<EvaluationType, string> = {
    [EvaluationType.EXAM]:       'Examen',
    [EvaluationType.QUIZ]:       'Quiz',
    [EvaluationType.PROJECT]:    'Projet',
    [EvaluationType.PRACTICAL]:  'Travaux pratiques',
    [EvaluationType.ASSIGNMENT]: 'Devoir',
}

// ─────────────────────────────────────────────────────────────────────────────

export enum ValidationStatus {
    EN_COURS   = 'en_cours',
    VALIDE     = 'valide',
    NON_VALIDE = 'non_valide',
    EN_ATTENTE = 'en_attente',
}

export const ValidationStatusLabels: Record<ValidationStatus, string> = {
    [ValidationStatus.EN_COURS]:   'En cours',
    [ValidationStatus.VALIDE]:     'Validé',
    [ValidationStatus.NON_VALIDE]: 'Non validé',
    [ValidationStatus.EN_ATTENTE]: 'En attente de validation',
}

export const ValidationStatusColors: Record<ValidationStatus, string> = {
    [ValidationStatus.EN_COURS]:   'blue',
    [ValidationStatus.VALIDE]:     'green',
    [ValidationStatus.NON_VALIDE]: 'red',
    [ValidationStatus.EN_ATTENTE]: 'yellow',
}

// ─────────────────────────────────────────────────────────────────────────────
// DOMAIN: Finance
// ─────────────────────────────────────────────────────────────────────────────

export enum PaymentPlanMode {
    TOTAL     = 'total',
    ECHELONNE = 'echelonne',
}

export const PaymentPlanModeLabels: Record<PaymentPlanMode, string> = {
    [PaymentPlanMode.TOTAL]:     'Paiement total',
    [PaymentPlanMode.ECHELONNE]: 'Paiement échelonné',
}

// ─────────────────────────────────────────────────────────────────────────────

export enum PaymentPlanStatus {
    PENDING   = 'pending',
    PARTIAL   = 'partial',
    COMPLETED = 'completed',
    OVERDUE   = 'overdue',
    CANCELLED = 'cancelled',
}

export const PaymentPlanStatusLabels: Record<PaymentPlanStatus, string> = {
    [PaymentPlanStatus.PENDING]:   'En attente',
    [PaymentPlanStatus.PARTIAL]:   'Partiellement payé',
    [PaymentPlanStatus.COMPLETED]: 'Payé intégralement',
    [PaymentPlanStatus.OVERDUE]:   'En retard',
    [PaymentPlanStatus.CANCELLED]: 'Annulé',
}

export const PaymentPlanStatusColors: Record<PaymentPlanStatus, string> = {
    [PaymentPlanStatus.PENDING]:   'gray',
    [PaymentPlanStatus.PARTIAL]:   'blue',
    [PaymentPlanStatus.COMPLETED]: 'green',
    [PaymentPlanStatus.OVERDUE]:   'red',
    [PaymentPlanStatus.CANCELLED]: 'gray',
}

// ─────────────────────────────────────────────────────────────────────────────

export enum TransactionMethod {
    CASH          = 'cash',
    BANK_TRANSFER = 'bank_transfer',
    CREDIT_CARD   = 'credit_card',
    MOBILE_MONEY  = 'mobile_money',
    CHECK         = 'check',
    OTHER         = 'other',
}

export const TransactionMethodLabels: Record<TransactionMethod, string> = {
    [TransactionMethod.CASH]:          'Espèces',
    [TransactionMethod.BANK_TRANSFER]: 'Virement bancaire',
    [TransactionMethod.CREDIT_CARD]:   'Carte bancaire',
    [TransactionMethod.MOBILE_MONEY]:  'Mobile Money',
    [TransactionMethod.CHECK]:         'Chèque',
    [TransactionMethod.OTHER]:         'Autre',
}

export const TransactionMethodIcons: Record<TransactionMethod, string> = {
    [TransactionMethod.CASH]:          '💵',
    [TransactionMethod.BANK_TRANSFER]: '🏦',
    [TransactionMethod.CREDIT_CARD]:   '💳',
    [TransactionMethod.MOBILE_MONEY]:  '📱',
    [TransactionMethod.CHECK]:         '📄',
    [TransactionMethod.OTHER]:         '💰',
}

/** Ces méthodes nécessitent une référence dans le formulaire */
export const MethodsRequiringReference = [
    TransactionMethod.BANK_TRANSFER,
    TransactionMethod.CREDIT_CARD,
    TransactionMethod.MOBILE_MONEY,
    TransactionMethod.CHECK,
]

// ─────────────────────────────────────────────────────────────────────────────

export enum TransactionStatus {
    PENDING   = 'pending',
    COMPLETED = 'completed',
    FAILED    = 'failed',
    CANCELLED = 'cancelled',
    REFUNDED  = 'refunded',
}

export const TransactionStatusLabels: Record<TransactionStatus, string> = {
    [TransactionStatus.PENDING]:   'En attente',
    [TransactionStatus.COMPLETED]: 'Complétée',
    [TransactionStatus.FAILED]:    'Échouée',
    [TransactionStatus.CANCELLED]: 'Annulée',
    [TransactionStatus.REFUNDED]:  'Remboursée',
}

export const TransactionStatusColors: Record<TransactionStatus, string> = {
    [TransactionStatus.PENDING]:   'yellow',
    [TransactionStatus.COMPLETED]: 'green',
    [TransactionStatus.FAILED]:    'red',
    [TransactionStatus.CANCELLED]: 'gray',
    [TransactionStatus.REFUNDED]:  'purple',
}

// ─────────────────────────────────────────────────────────────────────────────
// DOMAIN: Certification
// ─────────────────────────────────────────────────────────────────────────────

export enum CertificateStatus {
    EMIS    = 'emis',
    REVOQUE = 'revoque',
}

export const CertificateStatusLabels: Record<CertificateStatus, string> = {
    [CertificateStatus.EMIS]:    'Émis',
    [CertificateStatus.REVOQUE]: 'Révoqué',
}

export const CertificateStatusColors: Record<CertificateStatus, string> = {
    [CertificateStatus.EMIS]:    'green',
    [CertificateStatus.REVOQUE]: 'red',
}

// ─────────────────────────────────────────────────────────────────────────────

export enum BadgeStatus {
    ACTIF   = 'actif',
    EXPIRE  = 'expire',
    REVOQUE = 'revoque',
}

export const BadgeStatusLabels: Record<BadgeStatus, string> = {
    [BadgeStatus.ACTIF]:   'Actif',
    [BadgeStatus.EXPIRE]:  'Expiré',
    [BadgeStatus.REVOQUE]: 'Révoqué',
}

export const BadgeStatusColors: Record<BadgeStatus, string> = {
    [BadgeStatus.ACTIF]:   'green',
    [BadgeStatus.EXPIRE]:  'gray',
    [BadgeStatus.REVOQUE]: 'red',
}

// ─────────────────────────────────────────────────────────────────────────────

export enum CertificateMention {
    PASSABLE  = 'passable',
    BIEN      = 'bien',
    TRES_BIEN = 'tres_bien',
    EXCELLENT = 'excellent',
}

export const CertificateMentionLabels: Record<CertificateMention, string> = {
    [CertificateMention.PASSABLE]:  'Passable',
    [CertificateMention.BIEN]:      'Bien',
    [CertificateMention.TRES_BIEN]: 'Très Bien',
    [CertificateMention.EXCELLENT]: 'Excellent',
}

export const CertificateMentionColors: Record<CertificateMention, string> = {
    [CertificateMention.PASSABLE]:  'gray',
    [CertificateMention.BIEN]:      'blue',
    [CertificateMention.TRES_BIEN]: 'purple',
    [CertificateMention.EXCELLENT]: 'yellow',
}

export const CertificateMentionThresholds: Record<CertificateMention, number> = {
    [CertificateMention.PASSABLE]:  10,
    [CertificateMention.BIEN]:      12,
    [CertificateMention.TRES_BIEN]: 14,
    [CertificateMention.EXCELLENT]: 16,
}

/** Calcule la mention depuis une note /20 (miroir de CertificateMention::fromNote) */
export function mentionFromNote(note: number): CertificateMention {
    if (note >= 16) return CertificateMention.EXCELLENT
    if (note >= 14) return CertificateMention.TRES_BIEN
    if (note >= 12) return CertificateMention.BIEN
    return CertificateMention.PASSABLE
}

// ─────────────────────────────────────────────────────────────────────────────
// HELPER: Badge générique pour les statuts
// ─────────────────────────────────────────────────────────────────────────────

/** Tailwind classes par couleur pour les badges de statut */
export const ColorClasses: Record<string, string> = {
    green:  'bg-green-100 text-green-800',
    yellow: 'bg-yellow-100 text-yellow-800',
    red:    'bg-red-100 text-red-800',
    blue:   'bg-blue-100 text-blue-800',
    gray:   'bg-gray-100 text-gray-800',
    orange: 'bg-orange-100 text-orange-800',
    purple: 'bg-purple-100 text-purple-800',
}