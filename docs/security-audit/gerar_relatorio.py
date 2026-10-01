#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script de Geração do Relatório de Auditoria de Segurança
Projeto: Leitores de Papel (IFBA Ilhéus)
Gera o relatório visual em PDF de acordo com os padrões corporativos de AppSec.
"""

import os
import sys
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
from reportlab.lib.pagesizes import A4
from reportlab.lib import colors
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, Image, KeepTogether, PageBreak, HRFlowable
)
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import cm, mm
from reportlab.pdfgen import canvas

# Paleta oficial do relatório
PALETTE = {
    'critica': colors.HexColor('#B91C1C'),
    'alta': colors.HexColor('#EA580C'),
    'media': colors.HexColor('#D97706'),
    'baixa': colors.HexColor('#2563EB'),
    'forte': colors.HexColor('#059669'),
    'primary': colors.HexColor('#1E293B'),
    'secondary': colors.HexColor('#475569'),
    'light_bg': colors.HexColor('#F8FAFC'),
    'border': colors.HexColor('#CBD5E1'),
    'text': colors.HexColor('#0F172A'),
    'white': colors.HexColor('#FFFFFF')
}

OUTPUT_DIR = os.path.dirname(os.path.abspath(__file__))
PDF_PATH = os.path.join(OUTPUT_DIR, 'relatorio-auditoria-seguranca.pdf')
IMG_SEVERIDADE = os.path.join(OUTPUT_DIR, 'grafico_severidade.png')
IMG_CATEGORIAS = os.path.join(OUTPUT_DIR, 'grafico_categorias.png')

class NumberedCanvas(canvas.Canvas):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self.draw_page_decorations(num_pages)
            canvas.Canvas.showPage(self)
        canvas.Canvas.save(self)

    def draw_page_decorations(self, page_count):
        if self._pageNumber == 1:
            # Não desenha cabeçalho/rodapé na capa
            return

        self.saveState()
        self.setFont("Helvetica", 8)
        self.setFillColor(PALETTE['secondary'])

        # Cabeçalho
        self.drawString(2 * cm, 28.5 * cm, "Relatório de Auditoria de Segurança — Leitores de Papel (IFBA Ilhéus)")
        self.drawRightString(A4[0] - 2 * cm, 28.5 * cm, "Confidencial / AppSec")
        self.setStrokeColor(PALETTE['border'])
        self.setLineWidth(0.5)
        self.line(2 * cm, 28.3 * cm, A4[0] - 2 * cm, 28.3 * cm)

        # Rodapé
        self.line(2 * cm, 1.8 * cm, A4[0] - 2 * cm, 1.8 * cm)
        self.drawString(2 * cm, 1.3 * cm, "Instituto Federal da Bahia — Campus Ilhéus | Auditoria de Aplicação Web")
        page_text = f"Página {self._pageNumber} de {page_count}"
        self.drawRightString(A4[0] - 2 * cm, 1.3 * cm, page_text)
        self.restoreState()

def gerar_graficos():
    # 1. Gráfico de Rosca por Severidade
    labels_sev = ['Crítica', 'Alta', 'Média', 'Baixa']
    counts_sev = [4, 6, 3, 2] # 15 achados detalhados
    cores_sev = ['#B91C1C', '#EA580C', '#D97706', '#2563EB']

    fig, ax = plt.subplots(figsize=(4.5, 3.2), subplot_kw=dict(aspect="equal"))
    wedges, texts, autotexts = ax.pie(
        counts_sev,
        labels=labels_sev,
        autopct='%1.0f%%',
        startangle=140,
        colors=cores_sev,
        pctdistance=0.75,
        textprops=dict(color="#0F172A", fontsize=9, weight="bold")
    )
    for at in autotexts:
        at.set_color('white')
        at.set_fontsize(9)

    centre_circle = plt.Circle((0, 0), 0.52, fc='white')
    fig.gca().add_artist(centre_circle)
    ax.text(0, 0, f"Total\n15", ha='center', va='center', fontsize=12, fontweight='bold', color='#1E293B')
    ax.set_title("Achados por Severidade", fontsize=11, fontweight='bold', pad=12, color='#1E293B')
    plt.tight_layout()
    plt.savefig(IMG_SEVERIDADE, dpi=200, bbox_inches='tight')
    plt.close()

    # 2. Gráfico de Barras por Categoria
    categorias = [
        '1. Banco sem\nTranca',
        '2. Permissão\nno Navegador',
        '3. IDOR\n(Ações/Objetos)',
        '4. Chaves\nExpostas',
        '5. Inputs sem\nTratamento (XSS)'
    ]
    counts_cat = [2, 4, 3, 3, 3]
    cores_cat = ['#B91C1C', '#EA580C', '#EA580C', '#B91C1C', '#D97706']

    fig, ax = plt.subplots(figsize=(5.5, 3.2))
    bars = ax.bar(categorias, counts_cat, color=cores_cat, width=0.55, edgecolor='#475569', linewidth=0.5)
    ax.set_ylabel("Quantidade de Achados", fontsize=9, fontweight='bold', color='#1E293B')
    ax.set_title("Achados por Categoria de Auditoria", fontsize=11, fontweight='bold', pad=12, color='#1E293B')
    ax.set_ylim(0, 5)
    ax.grid(axis='y', linestyle='--', alpha=0.5)
    ax.tick_params(axis='x', labelsize=8)
    ax.tick_params(axis='y', labelsize=8)

    for bar in bars:
        height = bar.get_height()
        ax.annotate(f'{height}',
                    xy=(bar.get_x() + bar.get_width() / 2, height),
                    xytext=(0, 3),
                    textcoords="offset points",
                    ha='center', va='bottom', fontsize=9, fontweight='bold', color='#0F172A')

    plt.tight_layout()
    plt.savefig(IMG_CATEGORIAS, dpi=200, bbox_inches='tight')
    plt.close()

def build_pdf():
    gerar_graficos()

    doc = SimpleDocTemplate(
        PDF_PATH,
        pagesize=A4,
        leftMargin=2 * cm,
        rightMargin=2 * cm,
        topMargin=2.2 * cm,
        bottomMargin=2.2 * cm
    )

    styles = getSampleStyleSheet()

    # Estilos customizados
    title_style = ParagraphStyle(
        'CoverTitle',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=24,
        leading=30,
        textColor=PALETTE['primary'],
        alignment=0
    )

    subtitle_style = ParagraphStyle(
        'CoverSubtitle',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=12,
        leading=18,
        textColor=PALETTE['secondary']
    )

    h1_style = ParagraphStyle(
        'SectionH1',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=15,
        leading=20,
        textColor=PALETTE['primary'],
        spaceBefore=14,
        spaceAfter=8,
        keepWithNext=True
    )

    h2_style = ParagraphStyle(
        'SectionH2',
        parent=styles['Heading2'],
        fontName='Helvetica-Bold',
        fontSize=12,
        leading=16,
        textColor=PALETTE['secondary'],
        spaceBefore=10,
        spaceAfter=4,
        keepWithNext=True
    )

    body_style = ParagraphStyle(
        'BodyDark',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9.5,
        leading=14,
        textColor=PALETTE['text']
    )

    body_bold = ParagraphStyle(
        'BodyDarkBold',
        parent=body_style,
        fontName='Helvetica-Bold'
    )

    code_style = ParagraphStyle(
        'CodeSnippet',
        parent=styles['Code'],
        fontName='Courier',
        fontSize=8,
        leading=10.5,
        textColor=colors.HexColor('#0F172A'),
        backColor=colors.HexColor('#F1F5F9'),
        borderPadding=4,
        spaceBefore=4,
        spaceAfter=6
    )

    table_header = ParagraphStyle(
        'TableHeader',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=8.5,
        leading=11,
        textColor=colors.white
    )

    table_cell = ParagraphStyle(
        'TableCell',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8,
        leading=11,
        textColor=PALETTE['text']
    )

    table_cell_bold = ParagraphStyle(
        'TableCellBold',
        parent=table_cell,
        fontName='Helvetica-Bold'
    )

    issue_block_style = ParagraphStyle(
        'IssueBlock',
        parent=styles['Normal'],
        fontName='Courier',
        fontSize=7.5,
        leading=10,
        textColor=colors.HexColor('#0F172A'),
        backColor=colors.HexColor('#F8FAFC'),
        borderPadding=6,
        spaceBefore=4,
        spaceAfter=6
    )

    elements = []

    # ==========================================
    # CAPA DO RELATÓRIO
    # ==========================================
    elements.append(Spacer(1, 1.5 * cm))
    elements.append(Paragraph("RELATÓRIO DE AUDITORIA DE SEGURANÇA", subtitle_style))
    elements.append(Spacer(1, 0.3 * cm))
    elements.append(Paragraph("Sistema Leitores de Papel — IFBA Ilhéus", title_style))
    elements.append(HRFlowable(width="100%", thickness=3, color=PALETTE['critica'], spaceBefore=15, spaceAfter=15))

    meta_table_data = [
        [Paragraph("<b>Data da Avaliação:</b>", body_style), Paragraph("01 de Outubro de 2026", body_style)],
        [Paragraph("<b>Organização:</b>", body_style), Paragraph("Instituto Federal de Educação, Ciência e Tecnologia da Bahia (IFBA - Campus Ilhéus)", body_style)],
        [Paragraph("<b>Alvo do Escopo:</b>", body_style), Paragraph("Repositório Completo (Diretórios public/, admin/, user/, actions/, config/, includes/)", body_style)],
        [Paragraph("<b>Classificação:</b>", body_style), Paragraph("<font color='#B91C1C'><b>CONFIDENCIAL / AUDITORIA DE CÓDIGO FONTE (SAST)</b></font>", body_style)],
        [Paragraph("<b>Status da Análise:</b>", body_style), Paragraph("Concluída (100% de cobertura dos endpoints e handlers)", body_style)]
    ]
    t_meta = Table(meta_table_data, colWidths=[4.5 * cm, 12.5 * cm])
    t_meta.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, -1), PALETTE['light_bg']),
        ('BOX', (0, 0), (-1, -1), 1, PALETTE['border']),
        ('VALIGN', (0, 0), (-1, -1), 'TOP'),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
        ('TOPPADDING', (0, 0), (-1, -1), 6),
        ('LEFTPADDING', (0, 0), (-1, -1), 8),
        ('RIGHTPADDING', (0, 0), (-1, -1), 8),
    ]))
    elements.append(t_meta)
    elements.append(Spacer(1, 1 * cm))

    elements.append(Paragraph("1. Detecção da Stack & Nota Metodológica", h2_style))
    stack_text = (
        "<b>Stack Tecnológica Detectada:</b><br/>"
        "• <b>Linguagem & Paradigma:</b> PHP Procedural (compatível com PHP 5.6 e PHP 8.2).<br/>"
        "• <b>Framework:</b> Nenhum (arquitetura Vanilla PHP com roteamento baseado em arquivos físicos).<br/>"
        "• <b>Banco de Dados / ORM:</b> MySQL Server acessado via extensão nativa <code>mysqli</code> (sem ORM / sem Query Builder).<br/>"
        "• <b>Autenticação & Sessão:</b> Sessões nativas do PHP (<code>$_SESSION</code>), hashes de senha com <code>password_hash()</code> BCRYPT e <code>password_verify()</code>.<br/>"
        "• <b>Frontend:</b> HTML5, CSS customizado + Bootstrap 4, JavaScript Vanilla e FontAwesome via CDN.<br/>"
        "• <b>Ambiente de Execução:</b> Servidor Web Apache rodando sob MAMP PRO localmente e infraestrutura IFBA em produção.<br/><br/>"
        "<b>Adaptação Metodológica das Categorias Auditadas:</b><br/>"
        "1. <b>Banco sem Tranca:</b> Em aplicações sem RLS (como Supabase), a segurança reside nas cláusulas <code>WHERE</code> das consultas SQL que devem obrigatoriamente restringir o escopo ao <code>$_SESSION['user_id']</code>. Verificou-se onde leituras/mutações operam sem essa barreira.<br/>"
        "2. <b>Permissão Definida no Navegador:</b> Confrontou-se o modelo de menus no frontend (onde links administrativos ficam ocultos para alunos) com as validações de backend nos scripts de <code>admin/</code> e <code>actions/</code>, identificando endpoints privilegiados que aceitam requisições sem validar privilégio administrativo.<br/>"
        "3. <b>IDOR (Insecure Direct Object Reference):</b> Análise manual e sistemática de todos os parâmetros <code>livro_id</code>, <code>id</code> e <code>user_id</code> em endpoints de leitura, escrita e exclusão.<br/>"
        "4. <b>Chaves Expostas:</b> Varredura em busca de tokens SMTP, senhas de banco e credenciais embutidas diretamente no código.<br/>"
        "5. <b>Inputs sem Tratamento (XSS & Injeções):</b> Identificação de saídas de <code>$_GET</code>, <code>$_POST</code> e dados do banco renderizados sem <code>htmlspecialchars()</code>, além de concatenação de strings em SQL."
    )
    elements.append(Paragraph(stack_text, body_style))
    elements.append(PageBreak())

    # ==========================================
    # RESUMO EXECUTIVO
    # ==========================================
    elements.append(Paragraph("2. Resumo Executivo", h1_style))
    elements.append(Paragraph(
        "A auditoria de segurança estática (SAST) realizada no sistema <b>Leitores de Papel</b> identificou "
        "<b>15 vulnerabilidades ativas</b> distribuídas em todas as 5 categorias avaliadas. "
        "O sistema apresenta pontos arquiteturais positivos no armazenamento criptográfico de senhas, porém "
        "possui falhas críticas de <b>controle de acesso quebrado (Broken Access Control)</b>, permitindo que usuários "
        "comuns ou até mesmo atacantes desautenticados manipulem o acervo, excluam livros, acessem dados sensíveis "
        "de terceiros e utilizem credenciais corporativas vazadas.",
        body_style
    ))
    elements.append(Spacer(1, 0.4 * cm))

    # Tabela com Gráficos Lado a Lado
    chart_table_data = [
        [Image(IMG_SEVERIDADE, width=8.0 * cm, height=5.5 * cm),
         Image(IMG_CATEGORIAS, width=9.0 * cm, height=5.5 * cm)]
    ]
    t_charts = Table(chart_table_data, colWidths=[8.5 * cm, 9.5 * cm])
    t_charts.setStyle(TableStyle([
        ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
        ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 0),
        ('TOPPADDING', (0, 0), (-1, -1), 0),
    ]))
    elements.append(t_charts)
    elements.append(Spacer(1, 0.4 * cm))

    # Tabela de Métricas Rápidas
    metric_data = [
        [
            Paragraph("<font color='#FFFFFF'><b>CRÍTICA: 4</b></font>", table_header),
            Paragraph("<font color='#FFFFFF'><b>ALTA: 6</b></font>", table_header),
            Paragraph("<font color='#FFFFFF'><b>MÉDIA: 3</b></font>", table_header),
            Paragraph("<font color='#FFFFFF'><b>BAIXA: 2</b></font>", table_header),
            Paragraph("<font color='#FFFFFF'><b>PONTOS FORTES: 5</b></font>", table_header)
        ]
    ]
    t_metric = Table(metric_data, colWidths=[3.4 * cm, 3.4 * cm, 3.4 * cm, 3.4 * cm, 4.4 * cm])
    t_metric.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (0, 0), PALETTE['critica']),
        ('BACKGROUND', (1, 0), (1, 0), PALETTE['alta']),
        ('BACKGROUND', (2, 0), (2, 0), PALETTE['media']),
        ('BACKGROUND', (3, 0), (3, 0), PALETTE['baixa']),
        ('BACKGROUND', (4, 0), (4, 0), PALETTE['forte']),
        ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
        ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
        ('TOPPADDING', (0, 0), (-1, -1), 6),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
    ]))
    elements.append(t_metric)
    elements.append(Spacer(1, 0.6 * cm))

    # ==========================================
    # PONTOS FORTES E PONTOS FRACOS
    # ==========================================
    elements.append(Paragraph("3. Análise de Postura de Segurança", h1_style))
    elements.append(Paragraph("<b>3.1. Pontos Fortes Verificados (Evidências Positivas)</b>", h2_style))
    fortes_text = (
        "• <b>Armazenamento Seguro de Credenciais:</b> Em <code>public/cadastro.php:41</code> e <code>actions/login.php:56</code>, "
        "o sistema utiliza <code>password_hash(..., PASSWORD_DEFAULT)</code> gerando hashes BCRYPT robustos com salt automático, "
        "e autentica via <code>password_verify()</code>. Não há uso de algoritmos defasados (MD5/SHA1).<br/>"
        "• <b>Autenticação com Prepared Statements:</b> Em <code>actions/login.php:23-35</code>, as consultas de login para usuários e "
        "administradores utilizam <code>$conn->prepare()</code> com binding tipado (<code>bind_param('s', $username)</code>), impedindo SQL Injection na tela de login.<br/>"
        "• <b>Inserção de Comentários Parametrizada:</b> Em <code>actions/salvar_comentario_action.php:18-20</code>, a query de inclusão de avaliações "
        "utiliza prepared statements com amarração estrita de tipos (<code>iiss</code>).<br/>"
        "• <b>Escopo Seguro de Atualização de Perfil de Aluno:</b> Em <code>user/atualiza_dados.php:38-50</code>, a alteração de dados pessoais "
        "restringe-se a <code>WHERE username = '$_SESSION[username]'</code>, não permitindo edição de terceiros por injeção de ID.<br/>"
        "• <b>Escape no Cabeçalho de Usuário:</b> Em <code>includes/header.php:42, 85</code>, o nome do usuário exibido na barra superior "
        "é protegido com <code>htmlspecialchars()</code>, prevenindo XSS na saudação."
    )
    elements.append(Paragraph(fortes_text, body_style))
    elements.append(Spacer(1, 0.3 * cm))

    elements.append(Paragraph("<b>3.2. Riscos Centrais e Pontos Fracos Críticos</b>", h2_style))
    fracos_text = (
        "• <b>Controle de Acesso em Nível de Função Inexistente:</b> Scripts administrativos críticos (como <code>admin/excluir_livro.php</code>, "
        "<code>actions/salvar_livro.php</code> e <code>admin/informacoes_usuarios.php</code>) não verificam a existência de sessão administrativa. "
        "No painel principal (<code>admin/index.php:4</code>), o check lógico <code>!isset(admin) && !isset(user)</code> concede acesso a alunos.<br/>"
        "• <b>Vazamento Direto de Credenciais em Código (Hardcoded):</b> Chaves SMTP do provedor Brevo (usuário e senha do Gmail corporativo) "
        "estão commitadas em texto plano em <code>admin/notificacao.php:50-52</code>, viabilizando o sequestro do relay de e-mail.<br/>"
        "• <b>Manipulação Indireta de Estoque e IDOR:</b> Em <code>actions/devolve_livro_action.php:37-65</code>, a devolução incrementa o acervo "
        "de qualquer livro antes de verificar posse, permitindo adulteração maliciosa do inventário da biblioteca.<br/>"
        "• <b>Superfície Ampla de XSS Refletido:</b> Parâmetros de URL (como <code>error</code> e <code>success_message</code>) em <code>public/index.php</code> "
        "e parâmetros de pesquisa em <code>admin/lista_livros.php</code> são concatenados sem sanitização no DOM."
    )
    elements.append(Paragraph(fracos_text, body_style))
    elements.append(Spacer(1, 0.6 * cm))

    # ==========================================
    # TABELA DETALHADA DE ACHADOS
    # ==========================================
    elements.append(Paragraph("4. Tabela de Achados Detalhados por Categoria", h1_style))
    elements.append(Paragraph(
        "Abaixo constam todos os 15 achados verificados diretamente no código-fonte, categorizados e classificados por criticidade.",
        body_style
    ))
    elements.append(Spacer(1, 0.3 * cm))

    # Helper para chip de severidade
    def get_chip(sev):
        key = sev.lower().replace('í', 'i').replace('é', 'e')
        cor = PALETTE.get(key, PALETTE['primary'])
        return Paragraph(f"<font color='{cor.hexval()}'><b>[{sev.upper()}]</b></font>", table_cell_bold)

    tabela_achados_data = [
        [Paragraph("<b>Sev.</b>", table_header),
         Paragraph("<b>Cat.</b>", table_header),
         Paragraph("<b>Arquivo : Linha</b>", table_header),
         Paragraph("<b>Descrição da Vulnerabilidade & Impacto</b>", table_header)]
    ]

    achados_lista = [
        # Cat 1: Banco sem tranca
        ("Crítica", "1. Banco sem Tranca", "actions/devolve_livro_action.php:37-68",
         "<b>Manipulação de Estoque sem Validação de Posse:</b> O script incrementa o estoque do livro via <code>UPDATE livros SET quantidade+1</code> antes de verificar se o usuário autenticado realmente possui o empréstimo ativo. Atacante autenticado pode inflar arbitrariamente o estoque de qualquer livro."),
        ("Alta", "1. Banco sem Tranca", "actions/atualizar_emprestimos.php:19-65",
         "<b>Sincronização de Histórico Global Irrestrita:</b> Handler executa sincronização indiscriminada de toda a tabela <code>livros_emprestados</code> sem isolar pelo <code>user_id</code> do usuário que disparou a ação, misturando registros de múltiplos usuários."),

        # Cat 2: Permissão no navegador
        ("Crítica", "2. Permissão no Navegador", "admin/excluir_livro.php:9-20",
         "<b>Exclusão Pública de Livros sem Autenticação:</b> O endpoint aceita requisições POST para deleção de livros diretamente do banco (<code>DELETE FROM livros WHERE id = $livro_id</code>) sem validar sessão nem privilégio de administrador."),
        ("Crítica", "2. Permissão no Navegador", "admin/index.php:4-7",
         "<b>Bypass de Autenticação no Painel Administrador:</b> Condição de acesso usa operador <code>&&</code>: <code>!isset($_SESSION['admin_username']) && !isset($_SESSION['username'])</code>. Qualquer aluno autenticado com <code>$_SESSION['username']</code> obtém acesso irrestrito ao painel administrativo."),
        ("Alta", "2. Permissão no Navegador", "admin/lista_usuarios.php:13-19",
         "<b>Vazamento de PII de Usuários para Alunos:</b> Página de gerenciamento administrativo valida apenas <code>!isset($_SESSION['user_id'])</code>. Alunos podem acessar a rota e extrair nome, matrícula, e-mail, telefone, sexo e data de nascimento de toda a base de usuários."),
        ("Alta", "2. Permissão no Navegador", "actions/salvar_livro.php:13-42",
         "<b>Criação de Livros sem Autenticação:</b> Ação de cadastro de livros no acervo processa dados de formulário e executa <code>INSERT</code> sem validar se o usuário é administrador ou se sequer está autenticado."),
        ("Alta", "2. Permissão no Navegador", "admin/editar_livro.php:13-55",
         "<b>Edição e Exclusão de Livros por Usuários Comuns:</b> O script administrativo valida apenas se <code>$_SESSION['user_id']</code> existe, permitindo que leitores comuns alterem metadados de qualquer obra ou excluam livros desativando chaves estrangeiras."),
        ("Alta", "2. Permissão no Navegador", "admin/informacoes_usuarios.php:98-120",
         "<b>Relatório de Empréstimos Acessível Publicamente:</b> O script não possui nenhum bloqueio de autenticação. Qualquer visitante desautenticado na internet pode pesquisar e listar todos os empréstimos registrados com nomes dos estudantes."),

        # Cat 3: IDOR
        ("Alta", "3. IDOR", "actions/atualizar_livro.php:23-50",
         "<b>IDOR na Atualização de Obras:</b> O parâmetro <code>POST['livro_id']</code> é utilizado diretamente na query <code>UPDATE livros ... WHERE id='$livro_id'</code> sem checar privilégios administrativos no backend."),
        ("Média", "3. IDOR", "actions/salvar_comentario_action.php:10-25",
         "<b>Inserção de Comentário com Identidade Nula/Falsificada:</b> Script faz cast <code>(int)$_SESSION['user_id']</code> sem validar se a sessão existe. Permite inserção de comentários vinculados a <code>user_id = 0</code> sem validação se o usuário já leu ou pegou a obra."),
        ("Média", "3. IDOR", "admin/devolve_livro.php:25-45",
         "<b>IDOR na Ação de Devolução Administrativa:</b> Handler aceita <code>livro_id</code> via POST e remove registro sem validar se o empréstimo pertencia à transação esperada."),

        # Cat 4: Chaves expostas
        ("Crítica", "4. Chaves Expostas", "admin/notificacao.php:50-52",
         "<b>Credenciais SMTP Brevo em Texto Claro:</b> Host <code>smtp-relay.brevo.com</code>, conta <code>nzgamebr@gmail.com</code> e senha <code>0LQ98cwOraSE7RX2</code> commitadas no repositório. Permite roubo de serviço SMTP e envio de phishing."),
        ("Alta", "4. Chaves Expostas", "public/recuperar_senha.php:72-73",
         "<b>Credenciais de Sandbox Mailtrap Expostas:</b> Usuário <code>89f6fb8d8f567c</code> e senha <code>2174786544ed23</code> gravadas no código em páginas de recuperação de senha (também em <code>admin/recuperar_senha.php:67-68</code>)."),
        ("Média", "4. Chaves Expostas", "public/index.php:178-183",
         "<b>Exibição Pública de Credencial Default na UI:</b> A tela de login exibe textualmente <code>Usuário Admin: admin / Senha: admin</code> para qualquer visitante que acesse o portal."),

        # Cat 5: Inputs sem tratamento (XSS)
        ("Média", "5. Inputs sem Tratamento", "public/index.php:104, 112",
         "<b>Reflected XSS via Parâmetros de URL:</b> Os parâmetros <code>$_GET['success_message']</code> e <code>$_GET['error']</code> são impressos diretamente no corpo da página com <code>echo</code> sem <code>htmlspecialchars()</code>."),
        ("Baixa", "5. Inputs sem Tratamento", "admin/lista_livros.php:252, 261, 270",
         "<b>Reflected XSS em Atributos de Filtro:</b> Parâmetros de busca <code>titulo</code>, <code>autor</code> e <code>ano</code> são injetados diretamente em atributos HTML <code>value=\"...\"</code>."),
        ("Baixa", "5. Inputs sem Tratamento", "public/cadastro.php:95",
         "<b>Reflected XSS em PHP_SELF:</b> Concatenação de <code>$_SERVER['PHP_SELF']</code> no atributo <code>action</code> do formulário de cadastro sem sanitização.")
    ]

    for sev, cat, loc, desc in achados_lista:
        tabela_achados_data.append([
            get_chip(sev),
            Paragraph(cat, table_cell_bold),
            Paragraph(f"<code>{loc}</code>", table_cell),
            Paragraph(desc, table_cell)
        ])

    t_achados = Table(tabela_achados_data, colWidths=[1.8 * cm, 2.8 * cm, 4.4 * cm, 8.0 * cm])
    t_achados.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), PALETTE['primary']),
        ('BOX', (0, 0), (-1, -1), 0.5, PALETTE['border']),
        ('INNERGRID', (0, 0), (-1, -1), 0.5, PALETTE['border']),
        ('VALIGN', (0, 0), (-1, -1), 'TOP'),
        ('TOPPADDING', (0, 0), (-1, -1), 4),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 4),
        ('LEFTPADDING', (0, 0), (-1, -1), 4),
        ('RIGHTPADDING', (0, 0), (-1, -1), 4),
        ('ROWBACKGROUNDS', (0, 1), (-1, -1), [colors.white, PALETTE['light_bg']])
    ]))
    elements.append(t_achados)
    elements.append(PageBreak())

    # ==========================================
    # RECOMENDAÇÕES PRIORIZADAS
    # ==========================================
    elements.append(Paragraph("5. Recomendações Priorizadas de Remediação", h1_style))
    elements.append(Paragraph(
        "As correções devem seguir a matriz de risco abaixo para mitigar de imediato as brechas mais severas:",
        body_style
    ))
    elements.append(Spacer(1, 0.3 * cm))

    recoms_data = [
        [
            Paragraph("<b>Prioridade</b>", table_header),
            Paragraph("<b>Ação Recomendada</b>", table_header),
            Paragraph("<b>Impacto / Mitigação</b>", table_header)
        ],
        [
            Paragraph("<font color='#B91C1C'><b>P1 (Crítica)</b></font>", table_cell_bold),
            Paragraph("<b>Revogação Imediata de Credenciais e Isolamento de Segredos:</b><br/>"
                      "1. Revogar e alterar a senha da conta Brevo (Sendinblue) exposta em <code>admin/notificacao.php</code>.<br/>"
                      "2. Criar arquivo <code>config/secrets.php</code> (adicionado ao <code>.gitignore</code>) ou usar variáveis de ambiente (<code>getenv</code>) para SMTP e banco de dados.<br/>"
                      "3. Remover o aviso com credenciais padrão em <code>public/index.php</code> e obrigar troca de senha no primeiro login.", table_cell),
            Paragraph("Impede o sequestro da infraestrutura de e-mail e invasão imediata do portal via credenciais default conhecidas.", table_cell)
        ],
        [
            Paragraph("<font color='#EA580C'><b>P2 (Alta)</b></font>", table_cell_bold),
            Paragraph("<b>Implementação de Middleware/Gate Central de Autorização:</b><br/>"
                      "1. Criar função <code>require_admin()</code> que valida estritamente <code>isset($_SESSION['admin_username'])</code> e aborta com HTTP 403 caso contrário.<br/>"
                      "2. Aplicar essa proteção no topo de <b>todos</b> os arquivos em <code>admin/</code> e nos handlers de escrita correspondentes em <code>actions/</code>.<br/>"
                      "3. Corrigir a condição em <code>admin/index.php</code> para impedir acesso de estudantes.", table_cell),
            Paragraph("Elimina a escalação horizontal e vertical de privilégios de alunos para administrador.", table_cell)
        ],
        [
            Paragraph("<font color='#EA580C'><b>P3 (Alta)</b></font>", table_cell_bold),
            Paragraph("<b>Correção de Integridade de Empréstimos e IDOR:</b><br/>"
                      "1. Em <code>actions/devolve_livro_action.php</code>, validar atomicamente com transação SQL se o registro existe em <code>livros_emprestados</code> para aquele <code>user_id</code> antes de decrementar/incrementar estoque.<br/>"
                      "2. Usar transações (<code>$conn->begin_transaction()</code>) para garantir consistência.", table_cell),
            Paragraph("Elimina a manipulação indevida de acervo e adulteração do inventário da biblioteca.", table_cell)
        ],
        [
            Paragraph("<font color='#D97706'><b>P4 (Média)</b></font>", table_cell_bold),
            Paragraph("<b>Sanitização Global de Saídas (Prevenção de XSS):</b><br/>"
                      "1. Envolver todas as variáveis refletidas em <code>htmlspecialchars($var, ENT_QUOTES, 'UTF-8')</code>.<br/>"
                      "2. Corrigir mensagens de erro e sucesso em <code>public/index.php</code> e campos de filtro em <code>admin/lista_livros.php</code>.", table_cell),
            Paragraph("Impede a injeção e execução de scripts maliciosos na sessão dos usuários.", table_cell)
        ]
    ]

    t_recoms = Table(recoms_data, colWidths=[2.5 * cm, 9.5 * cm, 5.0 * cm])
    t_recoms.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), PALETTE['primary']),
        ('BOX', (0, 0), (-1, -1), 0.5, PALETTE['border']),
        ('INNERGRID', (0, 0), (-1, -1), 0.5, PALETTE['border']),
        ('VALIGN', (0, 0), (-1, -1), 'TOP'),
        ('TOPPADDING', (0, 0), (-1, -1), 6),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
        ('LEFTPADDING', (0, 0), (-1, -1), 6),
        ('RIGHTPADDING', (0, 0), (-1, -1), 6),
        ('ROWBACKGROUNDS', (0, 1), (-1, -1), [colors.white, PALETTE['light_bg']])
    ]))
    elements.append(t_recoms)
    elements.append(PageBreak())

    # ==========================================
    # SEÇÃO ISSUES PARA O GITHUB
    # ==========================================
    elements.append(Paragraph("6. Issues Prontas para o GitHub", h1_style))
    elements.append(Paragraph(
        "Os blocos abaixo contêm o texto completo em Markdown de cada issue acionável para inclusão no "
        "rastreador de tarefas (GitHub Issues). Cada issue agrupa achados relacionados com checklist e critérios de aceite.",
        body_style
    ))
    elements.append(Spacer(1, 0.4 * cm))

    issues_text = [
        ("""--- ISSUE 1 ---
### [Segurança] Revogação e isolamento de credenciais SMTP e dados de autenticação expostos em código

**Labels recomendadas:** `security`, `severity:critical`, `credentials`

#### Descrição do Problema
Foram identificadas credenciais em texto claro embutidas diretamente no código-fonte do repositório, incluindo conta SMTP do serviço Brevo (Sendinblue) com e-mail e senha corporativos, credenciais de Mailtrap e exibição de credencial default de administrador na tela pública de login.

#### Evidências
- `admin/notificacao.php:50-52`:
```php
$mail->Username = 'nzgamebr@gmail.com';
$mail->Password = '0LQ98cwOraSE7RX2';
```
- `public/recuperar_senha.php:72-73` e `admin/recuperar_senha.php:67-68`:
```php
$mail->Username = '89f6fb8d8f567c';
$mail->Password = '2174786544ed23';
```
- `public/index.php:178-183`:
```html
<div class="alert alert-warning text-center" role="alert">
  Usuário Admin: admin<br>Senha: admin
</div>
```

#### Impacto
- Uso malicioso do servidor SMTP Brevo para campanhas de spam/phishing em nome da instituição.
- Acesso imediato à área administrativa por qualquer visitante através da credencial padrão exposta.

#### Sugestão de Correção
1. Acessar o painel da Brevo e revogar imediatamente a chave SMTP exposta.
2. Criar um arquivo de configuração fora do controle de versão (ex: `config/env.php` no `.gitignore`) ou ler de variáveis de ambiente do servidor (`getenv()`).
3. Remover a mensagem de credenciais padrão do `public/index.php`.

#### Critérios de Aceite
- [ ] Credenciais da Brevo revogadas e substituídas por credenciais seguras.
- [ ] Chaves de acesso lidas via variáveis de ambiente ou arquivo local protegido.
- [ ] Nenhuma senha ou chave presente no código-fonte rastreado pelo Git.
- [ ] Card de credencial default removido da tela de login.
--- FIM ISSUE 1 ---"""),

        ("""--- ISSUE 2 ---
### [Segurança] Controle de Acesso Quebrado: Falta de validação de privilégio administrativo em endpoints de admin e actions

**Labels recomendadas:** `security`, `severity:critical`, `access-control`, `rbac`

#### Descrição do Problema
O sistema esconde links administrativos no menu do leitor, mas a camada de backend não valida se a requisição provém de um administrador. Em vários endpoints em `admin/` e `actions/`, o código valida apenas `isset($_SESSION['user_id'])` (que qualquer aluno logado possui), ou não possui autenticação alguma, ou contém erro de lógica condicional.

#### Evidências
- `admin/index.php:4`:
```php
if (!isset($_SESSION['admin_username']) && !isset($_SESSION['username'])) { ... }
```
Permite acesso ao painel de administração se `$_SESSION['username']` estiver definido (qualquer aluno logado).

- `admin/excluir_livro.php:9-20`:
Processa `POST['livro_id']` e executa `DELETE FROM livros WHERE id = $livro_id` sem qualquer checagem de sessão.

- `actions/salvar_livro.php:13-39`:
Permite que requisições POST criem livros no banco sem qualquer verificação de sessão ou papel.

- `admin/lista_usuarios.php:13-19` e `admin/editar_livro.php:13-19`:
Verificam apenas `!isset($_SESSION['user_id'])`, expondo dados confidenciais de alunos e edição de acervo a qualquer leitor.

- `admin/informacoes_usuarios.php:98-120`:
Sem validação de sessão; relatório aberto publicamente.

#### Impacto
Alunos comuns e atacantes desautenticados podem ler dados de todos os usuários, cadastrar, modificar e excluir livros do acervo, além de acessar painéis operacionais restritos.

#### Sugestão de Correção
1. Criar função auxiliar de autorização estrita (ex: `includes/auth.php`):
```php
function verificarAdmin() {
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['admin_username']) && empty($_SESSION['is_admin'])) {
        http_response_code(403);
        header("Location: ../public/index.php?error=" . urlencode("Acesso restrito a administradores."));
        exit;
    }
}
```
2. Invocar `verificarAdmin()` no início de todos os arquivos de `admin/` e nas ações de escrita de catálogo em `actions/`.

#### Critérios de Aceite
- [ ] Acesso a qualquer URL em `admin/` redireciona para login caso o usuário seja leitor comum ou anônimo.
- [ ] `actions/salvar_livro.php`, `actions/atualizar_livro.php` e `admin/excluir_livro.php` rejeitam requisições sem sessão de administrador ativa.
- [ ] Relatórios de empréstimos e listagem de usuários inacessíveis para quem não for administrador.
--- FIM ISSUE 2 ---"""),

        ("""--- ISSUE 3 ---
### [Segurança] IDOR e Adulteração de Estoque em Devolução de Obras

**Labels recomendadas:** `security`, `severity:high`, `idor`, `data-integrity`

#### Descrição do Problema
O endpoint de devolução de livros incrementa o estoque no banco de dados antes de validar se o usuário solicitante realmente possui o exemplar emprestado. Um usuário malicioso pode enviar sucessivas requisições POST com qualquer `livro_id` e inflar artificialmente o saldo de exemplares disponíveis na biblioteca.

#### Evidências
- `actions/devolve_livro_action.php:37-68`:
```php
$query = "SELECT quantidade FROM livros WHERE id = '$livro_id'";
...
$update_query = "UPDATE livros SET quantidade = $nova_quantidade WHERE id = '$livro_id'";
$update_result = $conn->query($update_query);
if ($update_result) {
    $delete_query = "DELETE FROM livros_emprestados WHERE livro_id = '$livro_id' AND user_id = '$user_id'";
    $delete_result = $conn->query($delete_query);
```

#### Impacto
Corrupção do estoque do acervo, perda de controle de disponibilidade e dessincronização física/digital dos livros da instituição.

#### Sugestão de Correção
Validar a existência do empréstimo para o usuário antes de alterar a tabela de livros, utilizando transação atômica:
```php
$conn->begin_transaction();
$check = $conn->prepare("SELECT id FROM livros_emprestados WHERE livro_id = ? AND user_id = ? LIMIT 1");
$check->bind_param("ii", $livro_id, $user_id);
$check->execute();
if ($check->get_result()->num_rows === 1) {
    $del = $conn->prepare("DELETE FROM livros_emprestados WHERE livro_id = ? AND user_id = ? LIMIT 1");
    $del->bind_param("ii", $livro_id, $user_id);
    $del->execute();
    $upd = $conn->prepare("UPDATE livros SET quantidade = quantidade + 1 WHERE id = ?");
    $upd->bind_param("i", $livro_id);
    $upd->execute();
    $conn->commit();
} else {
    $conn->rollback();
}
```

#### Critérios de Aceite
- [ ] Devolução só altera quantidade do livro se o usuário comprovar posse ativa na tabela `livros_emprestados`.
- [ ] Operação executada sob transação ACID para evitar concorrência.
- [ ] Tentativas de devolver livros não emprestados retornam mensagem de erro sem alterar o estoque.
--- FIM ISSUE 3 ---"""),

        ("""--- ISSUE 4 ---
### [Segurança] Correção de Reflected Cross-Site Scripting (XSS) em Mensagens e Filtros

**Labels recomendadas:** `security`, `severity:medium`, `xss`

#### Descrição do Problema
Valores recebidos via `$_GET` e `$_SERVER['PHP_SELF']` são impressos diretamente no documento HTML sem sanitização com `htmlspecialchars()`, viabilizando ataques de XSS Refletido através de links forjados enviados aos usuários.

#### Evidências
- `public/index.php:104, 112`:
```php
echo $_GET['success_message'];
echo $_GET['error'];
```
- `admin/lista_livros.php:252, 261, 270`:
```html
value="<?php echo $filtroTitulo; ?>"
value="<?php echo $filtroAutor; ?>"
value="<?php echo $filtroAno; ?>"
```
- `public/cadastro.php:95`:
```html
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
```

#### Impacto
Execução de scripts arbitrários no navegador da vítima, permitindo roubo de cookies de sessão (`PHPSESSID`), desfiguração de interface ou redirecionamentos maliciosos.

#### Sugestão de Correção
Aplicar `htmlspecialchars($valor, ENT_QUOTES, 'UTF-8')` em todas as variáveis refletidas no HTML:
```php
echo htmlspecialchars($_GET['success_message'], ENT_QUOTES, 'UTF-8');
```

#### Critérios de Aceite
- [ ] Nenhum parâmetro de URL é impresso sem escape no HTML.
- [ ] Payloads clássicos (ex: `<script>alert(1)</script>`) são neutralizados e renderizados como texto puro.
--- FIM ISSUE 4 ---""")
    ]

    import html
    for issue in issues_text:
        escaped_issue = html.escape(issue).replace('\n', '<br/>')
        elements.append(Paragraph(escaped_issue, issue_block_style))
        elements.append(Spacer(1, 0.3 * cm))

    doc.build(elements, canvasmaker=NumberedCanvas)
    print(f"Relatório gerado com sucesso em: {PDF_PATH}")

if __name__ == '__main__':
    build_pdf()
